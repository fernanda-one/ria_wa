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
        this.from = target;
    }

    async sendText(text, quoted = "") {
        const mentions = [...text.matchAll(/@(\d{0,16})/g)].map(
            (v) => v[1] + "@s.whatsapp.net"
        );
        this.from = await this.validateReceiver(this.from);
        return await this.velixs.sendMessage(
            this.from,
            { text, mentions },
            { quoted: quoted }
        );
    }

    async sendMedia(url, caption = "", options = {}, quoted = "") {
        console.log("🚀 ~ Client ~ sendMedia ~ quoted:", quoted);
        console.log("🚀 ~ Client ~ sendMedia ~ options:", options);
        console.log("🚀 ~ Client ~ sendMedia ~ caption:", caption);
        console.log("🚀 ~ Client ~ sendMedia ~ url:", url);
        let mime = options.file.mimetype.split("/")[0];
        this.from = await this.validateReceiver(this.from);
        const mentions = [...caption.matchAll(/@(\d{0,16})/g)].map(
            (v) => v[1] + "@s.whatsapp.net"
        );
        if (mime == "image") {
            console.log("🚀 ~ Client ~ sendMedia ~ mime:", mime);
            const data = await this.velixs.sendMessage(
                this.from,
                { image: { url: `${url}` }, caption: `${caption}`, mentions },
                { quoted: quoted }
            );
            console.log("🚀 ~ Client ~ sendMedia ~ data:", data);
        } else if (mime == "video") {
            await this.velixs.sendMessage(
                this.from,
                { video: { url: `${url}` }, caption: `${caption}`, mentions },
                { quoted: quoted }
            );
        } else if (mime == "audio") {
            const audiosend = await this.velixs.sendMessage(
                this.from,
                { audio: { url: `${url}` }, caption: `${caption}`, mentions },
                { quoted: quoted }
            );
            if (caption != "") {
                await this.velixs.sendMessage(
                    this.from,
                    { text: caption },
                    { quoted: audiosend }
                );
            }
        } else if (mime == "file") {
            const buffer = await axios.get(url, {
                responseType: "arraybuffer",
            });
            const name = url.split("/").pop();
            const type = buffer.headers["content-type"];
            const sendfile = await this.velixs.sendMessage(
                this.from,
                {
                    document: buffer.data,
                    fileName: name,
                    mimetype: type,
                    mentions,
                },
                { quoted: quoted }
            );
            if (caption != "") {
                await this.velixs.sendMessage(
                    this.from,
                    { text: caption },
                    { quoted: sendfile }
                );
            }
        }
    }

    async sendButton({ image_url, text, footer, buttons }) {
        this.from = await this.validateReceiver(this.from);
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
            return await this.velixs.sendMessage(this.from, buttonMessage);
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
        this.from = await this.validateReceiver(this.from);
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

            return await this.velixs.sendMessage(this.from, listMessage);
        }
    }

    // async sendButton({ image_url, text, footer, buttons }) {
    //     this.from = await this.validateReceiver(this.from);
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
    //         return await this.velixs.sendMessage(this.from, buttonMessage);
    //     }
    // }

    async reply(text, quoted) {
        const mentions = [...text.matchAll(/@(\d{0,16})/g)].map(
            (v) => v[1] + "@s.whatsapp.net"
        );
        return await this.velixs.sendMessage(
            this.from,
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
