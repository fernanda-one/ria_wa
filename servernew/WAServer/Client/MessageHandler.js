const serialize = require('./SerializeBeta.js');
const RespondersDatabase = require('../../app/database/responders.db.js');
const SessionsDatabase = require('../../app/database/sessions.db.js');
const Client = require('./Client.js');
const { logger } = require('../../app/lib/myf.velixs.js');
const axios = require('axios');
const { commands, checkSessionCommands } = require('../../app/config/commands.js');

class Message {
    constructor(velixs, message, session) {
        this.velixs = velixs;
        this.message = message;
        this.session = session;
        this.isResponder = 0;
        this.isResponder_contains = false;
    }

    async mainHandler() {
        try {
            if (!this.message) return;
            const msg = this.message;
            if (msg.key && msg.key.remoteJid === 'status@broadcast') return;
            if (!msg.message) return;
            const serialized = await serialize(this.velixs, msg);
            const client = new Client(this.velixs, serialized.msg);
            const webhook = await this.getWebhook();

            if (!webhook) return this.messageHandler(serialized, client);
        } catch (error) {
            // Handle the error
        }
    }

    async messageHandler(serialized, client) {
        let isGroup = serialized.isGroup;
        let fromMe = serialized.fromMe;
        const text = serialized.type === 'buttonsResponseMessage' 
            ? serialized.message[serialized.type].selectedButtonId 
            : serialized.type === 'listResponseMessage' 
                ? serialized.message[serialized.type].singleSelectReply.selectedRowId 
                : serialized.type === 'templateButtonReplyMessage' 
                    ? serialized.message[serialized.type].selectedId 
                    : serialized.text;
        const args = text.trim().split(/ +/).slice(1);
        const command = text.toLowerCase().split(/ +/).shift().toLowerCase();

        if (text === '') return;

        let sessionData = await new SessionsDatabase().getSession(this.session);
        if (sessionData && this.validateUrl(sessionData)) {
            axios.post(sessionData, { 'message': text, 'from': serialized.from, 'isGroup': isGroup, 'isMe': fromMe })
                .then(response => {
                    let data = JSON.parse(JSON.stringify(response.data));
                    if (data.status !== 'false') {
                        let message = JSON.parse(data.message);
                        switch (message.message_type) {
                            case 'text':
                                client.sendText(message.text);
                                break;
                            case 'media':
                                let fileOptions = { 'file': { 'mimetype': '' + message.media_type } };
                                client.sendMedia(message.media, message.caption, fileOptions);
                                break;
                        }
                    }
                })
                .catch(error => {
                    // Handle error
                });
        }

        this.isResponder = await new RespondersDatabase().findAutoResponder({ 'keyword': text, 'session_id': this.session });
        if (this.isResponder.length === 0) {
            this.isResponder = await new RespondersDatabase().find({ 'session_id': this.session });
            this.isResponder_contains = true;
        }

        if (this.isResponder.length > 0) {
            this.isResponder.forEach(async (responder) => {
                if (this.isResponder_contains) {
                    if (!text.includes(responder.keyword)) return;
                }
                let response = JSON.parse(responder.message);
                if (responder.message_type === 'group' && !isGroup) return;
                if (responder.reply_when === 'personal' && isGroup) return;

                switch (responder.message_type) {
                    case 'text':
                        client.sendText(response.text, response.quoted ? serialized : '');
                        break;
                    case 'media':
                        let mediaOptions = { 'file': { 'mimetype': '' + response.media_type } };
                        client.sendMedia(response.media, response.caption, mediaOptions, response.quoted ? serialized : '');
                        break;
                    case 'button':
                        client.sendButton({ 'text': response.text, 'footer': response.footer, 'buttons': response.buttons });
                        break;
                    case 'list':
                        client.sendListButton({ 'title': response.title, 'text': response.message, 'footer': response.footer, 'buttonText': response.button_text, 'sections': response.sections });
                        break;
                }
            });
        }

        try {
            let cmd = Array.from(commands.values()).find(cmd => cmd.cmd.some(alias => alias.toLowerCase() === command));
            if (cmd) {
                if (cmd.global_feature) {
                    cmd.run(this.velixs, serialized, args);
                } else if (checkSessionCommands(this.session, cmd.name)) {
                    cmd.run(this.velixs, serialized, args);
                }
            }
        } catch (error) {
            // Handle error
        }
    }

    async validateUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (error) {
            return false;
        }
    }

    async getWebhook() {
        // Placeholder for the actual implementation of getWebhook
        return null;
    }
}

module.exports = Message;
