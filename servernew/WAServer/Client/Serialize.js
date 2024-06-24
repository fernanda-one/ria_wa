const {
    downloadContentFromMessage,
    getContentType,
    getDevice,
    isJidBroadcast,
    isJidGroup,
    jidNormalizedUser,
    toBuffer,
} = require("@whiskeysockets/baileys");
const moment = require("moment-timezone");
moment.tz.setDefault("Asia/Jakarta").locale("id");

const Client = require("./Client.js");
const { PREFIX } = process.env;

class Serialize extends Client {
    constructor() {
        super();
    }

    async serial(client, msg) {
        const m = {};
        if (msg.key) {
            const { id, fromMe, remoteJid, participant } = msg.key;
            m.id = id;
            m.fromMe = fromMe;
            m.from = remoteJid;
            m.sender = fromMe
                ? jidNormalizedUser(client.user.id) || ""
                : isJidGroup(remoteJid)
                    ? jidNormalizedUser(participant)
                    : isJidBroadcast(remoteJid)
                        ? jidNormalizedUser(participant)
                        : jidNormalizedUser(remoteJid);
            m.isGroupMsg = m.from.endsWith("@g.us");
            m.device = getDevice(m.id);
        }
        m.botNumber = `${client.user.id.split(":")[0]}@s.whatsapp.net`;
        m.type = getContentType(msg.message);
        m.pushname = msg.pushName;
        m.t = msg.messageTimestamp;
        m.time = moment(m.t * 1000).format("DD/MM/YY HH:mm:ss");

        if (["ephemeralMessage", "viewOnceMessage"].includes(m.type)) {
            msg.message = msg.message[m.type].message;
            m.type = getContentType(msg.message);
        }

        m.body = this.getBody(m.type, msg.message);
        m.mentions = msg.message[m.type]?.contextInfo ? msg.message[m.type].contextInfo.mentionedJid : [];

        if (msg.message[m.type]?.contextInfo?.quotedMessage) {
            m.quoted = this.getQuotedMessage(client, msg, m);
        } else {
            m.quoted = null;
        }

        if (m.isGroupMsg) {
            m.group = await this.getGroupMetadata(client, m.from, m.sender, m.botNumber);
        } else {
            m.group = null;
        }

        m.isMedia = this.getMediaTypes(m.type, m.quoted);
        m.isCmd = m.body.startsWith(PREFIX);
        m.command = m.isCmd ? m.body.slice(1).trim().split(/ +/).shift().toLowerCase() : null;
        m.msg = msg;
        m.download = (path = null) => this.downloadMedia(msg.message, path);

        return m;
    }

    getBody(type, message) {
        switch (type) {
            case "conversation":
                return message.conversation;
            case "imageMessage":
                return message.imageMessage.caption;
            case "videoMessage":
                return message.videoMessage.caption;
            case "extendedTextMessage":
                return message.extendedTextMessage.text;
            case "buttonsResponseMessage":
                return message.buttonsResponseMessage.selectedButtonId;
            case "listResponseMessage":
                return message.listResponseMessage.singleSelectReply.selectedRowId;
            case "templateButtonReplyMessage":
                return message.templateButtonReplyMessage.selectedId;
            case "messageContextInfo":
                return message.listResponseMessage?.singleSelectReply.selectedRowId ||
                       message.buttonsResponseMessage?.selectedButtonId ||
                       message.text;
            default:
                return "";
        }
    }

    getQuotedMessage(client, msg, m) {
        const quotedMessage = msg.message[m.type].contextInfo.quotedMessage;
        const stanzaId = msg.message[m.type].contextInfo.stanzaId;
        const participant = msg.message[m.type].contextInfo.participant;

        return {
            message: quotedMessage,
            key: {
                id: stanzaId,
                fromSelf: participant === `${client.user.id.split(":")[0]}@s.whatsapp.net`,
                remoteJid: m.from,
            },
            type: getContentType(quotedMessage),
            device: getDevice(stanzaId),
            delete: () => client.sendMessage(m.from, { delete: { stanzaId, fromSelf: m.fromMe, remoteJid: m.from } }),
            download: (path = null) => this.downloadMedia(quotedMessage, path),
        };
    }

    async getGroupMetadata(client, from, sender, botNumber) {
        const groupMetadata = await client.groupMetadata(from);
        const admins = groupMetadata.participants
            .filter((v) => v.admin !== null)
            .map((x) => x.id);

        return {
            groupMetadata,
            admins,
            isSenderGroupAdmin: admins.includes(sender),
            isBotGroupAdmin: admins.includes(botNumber),
        };
    }

    getMediaTypes(type, quoted) {
        const isMedia = {
            isImage: type === "imageMessage",
            isVideo: type === "videoMessage",
            isAudio: type === "audioMessage",
            isSticker: type === "stickerMessage",
            isContact: type === "contactMessage",
            isLocation: type === "locationMessage",
        };

        if (quoted) {
            const quotedType = Object.keys(quoted.message)[0];
            isMedia.isQuotedImage = quotedType === "imageMessage";
            isMedia.isQuotedVideo = quotedType === "videoMessage";
            isMedia.isQuotedAudio = quotedType === "audioMessage";
            isMedia.isQuotedSticker = quotedType === "stickerMessage";
            isMedia.isQuotedContact = quotedType === "contactMessage";
            isMedia.isQuotedLocation = quotedType === "locationMessage";
        }

        return isMedia;
    }

    async downloadMedia(msg, returnType, pathFile) {
        try {
            const type = Object.keys(msg)[0];
            const mimeMap = {
                imageMessage: "image",
                videoMessage: "video",
                stickerMessage: "sticker",
                documentMessage: "document",
                audioMessage: "audio",
            };

            const stream = await downloadContentFromMessage(msg[type], mimeMap[type]);

            if (returnType === "stream") {
                return stream;
            }

            let buffer = Buffer.from([]);
            for await (const chunk of stream) {
                buffer = Buffer.concat([buffer, chunk]);
            }

            if (pathFile) {
                await fs.promises.writeFile(pathFile, buffer);
                return pathFile;
            } else {
                return buffer;
            }
        } catch (error) {
            console.error("Error downloading media:", error);
            return null;
        }
    }
}

module.exports = Serialize;
