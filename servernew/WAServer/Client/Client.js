const {
    default: pkg,
    downloadContentFromMessage,
    toBuffer,
    generateThumbnail,
    generateWAMessageFromContent,
    prepareWAMessageMedia,
    proto,
} = require("@whiskeysockets/baileys");

const axios = require("axios");
const fs = require("fs");

class Client {
    constructor(velixs, target) {
        this.velixs = velixs;
        this.target = target;
    }

    async sendText(text, quoted = "") {
        try {
            this.target = await this.validateReceiver(this.target);
            const mentions = [...text.matchAll(/@(\d{0,16})/g)].map(
                (v) => v[1] + "@s.whatsapp.net"
            );
            const message = { text, mentions };
            return await this.velixs.sendMessage(this.target, message, {
                quoted,
            });
        } catch (error) {
            console.error("Error sending text message:", error);
            return null;
        }
    }

    async sendMedia(url, caption = "", options = {}, quoted = "") {
        try {
            const mime = options.file.mimetype.split("/")[0];
            this.target = await this.validateReceiver(this.target);
            const mentions = [...caption.matchAll(/@(\d{0,16})/g)].map(
                (v) => v[1] + "@s.whatsapp.net"
            );

            let mediaMessage;
            if (mime === "image") {
                mediaMessage = { image: { url }, caption, mentions };
            } else if (mime === "video") {
                mediaMessage = { video: { url }, caption, mentions };
            } else if (mime === "audio") {
                mediaMessage = { audio: { url }, caption, mentions };
            } else if (mime === "file") {
                const buffer = await this.downloadFile(url);
                mediaMessage = {
                    document: buffer,
                    fileName: url.split("/").pop(),
                    mimetype: options.file.mimetype,
                    mentions,
                };
            }

            const sentMessage = await this.velixs.sendMessage(
                this.target,
                mediaMessage,
                { quoted }
            );
            if (caption && mime === "audio") {
                await this.velixs.sendMessage(
                    this.target,
                    { text: caption },
                    { quoted: sentMessage }
                );
            }
            return sentMessage;
        } catch (error) {
            console.error("Error sending media message:", error);
            return null;
        }
    }

    async sendButton({ image_url, text, footer, buttons }) {
        this.target = await this.validateReceiver(this.target);
        const mentions = [...text.matchAll(/@(\d{0,16})/g)].map(
            (v) => v[1] + "@s.whatsapp.net"
        );
        buttons = buttons.map((button, index) => {
            return {
                buttonId: button.id,
                buttonText: { displayText: button.display },
                type: 1,
            };
        });

        if (image_url) {
            return false;
        } else {
            const buttonMessage = {
                text: text,
                footer: footer,
                buttons: buttons,
                mentions: mentions,
                headerType: 1,
            };
            return await this.velixs.sendMessage(this.target, buttonMessage);
        }
    }

    async sendListButton({
        image_url,
        title,
        button_text,
        text,
        footer,
        sections,
    }) {
        this.target = await this.validateReceiver(this.target);
        const mentions = [...text.matchAll(/@(\d{0,16})/g)].map(
            (v) => v[1] + "@s.whatsapp.net"
        );

        if (image_url) {
            return false;
        } else {
            const listMessage = {
                text: text,
                footer: footer,
                title: title,
                buttonText: button_text,
                mentions: mentions,
                sections,
            };

            return await this.velixs.sendMessage(this.target, listMessage);
        }
    }

    // async sendButton({ image_url, text, footer, buttons }) {
    //     this.target = await this.validateReceiver(this.target);
    //     const mentions = [...text.matchAll(/@(\d{0,16})/g)].map((v) => v[1] + "@s.whatsapp.net");
    //     buttons = buttons.map((button, index) => {
    //         if (button.type == 'urlButton') {
    //             return { index: index + 1, urlButton: { displayText: button.display, url: button.url } };
    //         } else if (button.type == 'callButton') {
    //             return { index: index + 1, callButton: { displayText: button.display, phoneNumber: button.phoneNumber } };
    //         } else if (button.type == 'quickReplyButton') {
    //             return { index: index + 1, quickReplyButton: { displayText: button.display, id: button.id } };
    //         }
    //     });
    //     if (image_url) {
    //     } else {
    //         const buttonMessage = {
    //             text: text,
    //             footer: footer,
    //             templateButtons: buttons,
    //             headerType: 4,
    //             mentions: mentions,
    //             viewOnce: true, // Sementara
    //         };
    //         return await this.velixs.sendMessage(this.target, buttonMessage);
    //     }
    // }

    async reply(text, quoted) {
        const mentions = [...text.matchAll(/@(\d{0,16})/g)].map(
            (v) => v[1] + "@s.whatsapp.net"
        );
        return await this.velixs.sendMessage(
            this.target,
            { text, mentions },
            { quoted }
        );
    }

    async isWhatsapp(number) {
        if (number.includes("@g.us")) {
            try {
                await this.velixs.groupMetadata(number);
                return number;
            } catch (error) {
                return false;
            }
        } else {
            if (number.includes("@")) {
                number = number.split("@")[0];
            }
            let s = await this.velixs.onWhatsApp(number);
            if (s.length > 0) {
                return number;
            } else {
                return false;
            }
        }
    }

    async validateReceiver(id) {
        let isGroup = id.includes("@g.us");
        if (isGroup) {
            return id;
        } else {
            let number = id.replace(/[^0-9]/g, "");
            if (number.includes("@")) {
                return number;
            } else {
                return number + "@s.whatsapp.net";
            }
        }
    }

    async downloadMedia(msg, urlFile) {
        return new Promise(async (resolve, reject) => {
            try {
                const type = Object.keys(msg)[0];
                const mimeMap = {
                    imageMessage: "image",
                    videoMessage: "video",
                    stickerMessage: "sticker",
                    documentMessage: "document",
                    audioMessage: "audio",
                };
                const stream = await downloadContentFromMessage(
                    msg[type],
                    mimeMap[type]
                );
                let buffer = await toBuffer(stream);
                if (urlFile) {
                    fs.promises
                        .writeFile(urlFile, buffer)
                        .then(resolve(urlFile));
                } else {
                    resolve(stream);
                }
            } catch (error) {
                reject(error);
            }
        });
    }
}

module.exports = Client;
