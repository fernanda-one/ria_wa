const { validationResult } = require('express-validator');
const SessionsDatabase = require('../database/sessions.db.js');
const UsersDatabase = require('../database/users.db.js');
const SessionConnection = require('../../WAServer/session.js');
const Client = require('../../WAServer/Client/Client.js');
const { commands, actSessionCommands, getSessionCommands,loadCommands } = require('../config/commands.js');
class ApiController extends SessionsDatabase {
    constructor() {
        super();
    }

    async index(req, res) {
        res.json({ message: 'Server Running!' });
    }

    async sendMessage(req, res) {
        const { receiver, data } = req.body;
        const asolo = await this.asolo(req, res);
        if (!asolo.status) return res.status(400).json(asolo);
        try {
            let client = new Client(asolo.session, receiver);
            if (!await client.isWhatsapp(receiver)) return res.status(400).json({ status: false, message: 'Invalid WhatsApp number.' + receiver });
            await client.sendText(data.message).then(() => {
                return res.status(200).json({ status: true, message: 'Message sent.' });
            }).catch(() => {
                return res.status(400).json({ status: false, message: 'Failed to send message.' });
            });
        } catch (e) {
            return res.status(400).json({ status: false, message: 'Failed to send message.' });
        }
    }

    async sendMedia(req, res) {
        const { receiver, data, waiting } = req.body
        const asolo = await this.asolo(req, res);
        if (!asolo.status) return res.status(400).json(asolo);
        try {
            let client = new Client(asolo.session, receiver);
            if (!await client.isWhatsapp(receiver)) return res.status(400).json({ status: false, message: 'Invalid WhatsApp number.' });
            var opts = { file: { mimetype: `${data.media_type}` } };
            if (waiting) {
                setTimeout(async () => {
                    await client.sendMedia(data.url, data.caption, opts)
                }, waiting);

                return res.status(200).json({ status: true, message: 'Media is being sent.' });
            } else {
                await client.sendMedia(data.url, data.caption, opts).then(() => {
                    return res.status(200).json({ status: true, message: 'Media sent.' });
                }).catch((e) => {
                    return res.status(400).json({ status: false, message: 'Failed to send media.' });
                });
            }
        } catch (e) {
            return res.status(400).json({ status: false, message: 'Failed to send media.' });
        }
    }

    async sendButton(req, res) {
        const { receiver, data } = req.body
        const asolo = await this.asolo(req, res);
        if (!asolo.status) return res.status(400).json(asolo);
        try {
            let client = new Client(asolo.session, receiver);
            if (!await client.isWhatsapp(receiver)) return res.status(400).json({ status: false, message: 'Invalid WhatsApp number.' });
            await client.sendButton({
                image_url: data.image_url,
                text: data.message,
                footer: data.footer,
                buttons: data.buttons
            }).then(() => {
                return res.status(200).json({ status: true, message: 'Button sent.' });
            }).catch((e) => {
                return res.status(400).json({ status: false, message: 'Failed to send button.', error: e });
            });
        } catch (e) {
            return res.status(400).json({ status: false, message: 'Failed to send button.', error: e });
        }
    }

    async sendListButton(req, res) {
        const { receiver, data } = req.body
        const asolo = await this.asolo(req, res);
        if (!asolo.status) return res.status(400).json(asolo);
        try {
            let client = new Client(asolo.session, receiver);
            if (!await client.isWhatsapp(receiver)) return res.status(400).json({ status: false, message: 'Invalid WhatsApp number.' });
            await client.sendListButton({
                image_url: data.image_url,
                title: data.title,
                text: data.message,
                footer: data.footer,
                button_text: data.buttonText,
                sections: data.sections
            }).then(() => {
                return res.status(200).json({ status: true, message: 'List Message sent.' });
            }).catch((e) => {
                return res.status(400).json({ status: false, message: 'Failed to send List Message.' });
            });
        } catch (e) {
            return res.status(400).json({ status: false, message: 'Failed to send List Message.' });
        }
    }

    async fetchGroup(req, res) {
        const asolo = await this.asolo(req, res);
        if (!asolo.status) return res.status(400).json(asolo);
        try {
            let client = await asolo.session;
            // fetch group list
            let chats = await client.groupFetchAllParticipating();
            let groups = Object.values(chats).map((v) => {
                return {
                    name: v.subject,
                    id: v.id,
                };
            });

            return res.status(200).json({
                status: true,
                data: groups
            });
        } catch (e) {
            return res.status(400).json({ status: false, message: 'Failed to fetch group.' });
        }
    }

    async fetchGroupMembers(req, res) {
        const { id } = req.body;
        const asolo = await this.asolo(req, res);
        if (!asolo.status) return res.status(400).json(asolo);
        try {
            let client = await asolo.session;
            // fetch group list
            let chats = await client.groupFetchAllParticipating();
            let group = chats[id];
            if (!group) return res.status(400).json({ status: false, message: 'Group not found.' });
            let members = Object.values(group.participants).map((v) => {
                return {
                    id: v.id
                };
            });
            return res.status(200).json({
                status: true,
                data: members
            });
        } catch (e) {
            return res.status(400).json({ status: false, message: 'Failed to fetch group members.' });
        }
    }

    async trigerCampaigns(req, res) {
        const asolo = await this.asolo(req, res);
        if (!asolo.status) return res.status(400).json(asolo);
        try {
            const eventEmitter = require('../../app/lib/Event.js');
            eventEmitter.emit('campaigns', asolo.dbsession.id);
            return res.status(200).json({
                status: true,
            });
        } catch (e) {
            return res.status(400).json({
                status: false,
                message: 'Failed to triger campaigns.'
            });
        }
    }

    async getPlugins(req, res) {
        const { api_key } = req.body;
        try {
            let dbsession = await this.findApiKey(api_key);
            if (!dbsession) return res.status(400).json({ status: false, message: 'Invalid API Key.' });

            return res.status(200).json({
                status: true,
                data: {
                    commands: Array.from(commands.values()),
                    session_commands: await getSessionCommands(dbsession.id)
                }
            });
        } catch (e) {
            return res.status(400).json({
                status: false,
                message: 'Failed to get plugins.'
            });
        }
    }

    async reloadPlugins(req, res) {
        try {
            loadCommands();
            return res.status(200).json({
                status: true,
                message: 'Plugins reloaded.'
            });
        } catch (e) {
            return res.status(400).json({
                status: false,
                message: 'Failed to reload plugins.'
            });
        }
    }

    async actPlugin(req, res) {
        const { api_key } = req.body;
        try {
            let dbsession = await this.findApiKey(api_key);
            if (!dbsession) return res.status(400).json({ status: false, message: 'Invalid API Key.' });

            const { commands_name, status } = req.body;
            let command = commands.get(commands_name.toLowerCase().replace(/\s/g, ''));
            if (!command) return res.status(400).json({ status: false, message: 'Invalid command.' });

            if (status == 'inactive') {
                await actSessionCommands(dbsession.id, 'delete', commands_name.toLowerCase().replace(/\s/g, '')).then((data) => {
                    if (!data) return res.status(400).json({ status: false, message: 'Failed to activate plugin, because device is not connected.' });
                    return res.status(200).json({
                        status: true,
                    });
                }).catch(() => {
                    return res.status(400).json({
                        status: false,
                        message: 'Failed to inactive plugin.'
                    });
                });
            } else {
                await actSessionCommands(dbsession.id, 'add', commands_name.toLowerCase().replace(/\s/g, '')).then((data) => {
                    if (!data) return res.status(400).json({ status: false, message: 'Failed to activate plugin, because device is not connected.' });
                    return res.status(200).json({
                        status: true,
                    });
                }).catch(() => {
                    return res.status(400).json({
                        status: false,
                        message: 'Failed to activate plugin.'
                    });
                });
            }
        } catch (e) {
            return res.status(400).json({
                status: false,
                message: 'Server error.'
            });
        }
    }

    async asolo(req, res, validator = true) {
        const { api_key } = req.body;
        try {
            const socket = req.app.get('socket');
            if (validator) {
                const result = validationResult(req);
                if (!result.isEmpty()) {
                    return {
                        status: false,
                        message: 'Invalid request.',
                        errors: result.array()
                    }
                }
            }
            let dbsession = await this.findApiKey(api_key);
            if (!dbsession) return { status: false, message: 'Invalid API Key.' };
            if (dbsession.status !== 'CONNECTED') return { status: false, message: 'Device is stopped.' };

            let session = await new SessionConnection(socket).getSession(dbsession.id);
            if (!session) return { status: false, message: 'Session is stopped.' };
            return { status: true, session: session, dbsession };
        } catch (e) {
            return { status: false, message: 'Something went wrong.' };
        }
    }

}

module.exports = ApiController;
