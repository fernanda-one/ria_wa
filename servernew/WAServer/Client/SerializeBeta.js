const {
    extractMessageContent,
    jidNormalizedUser,
    proto,
} = require("@whiskeysockets/baileys");

const getTypeMessage = (message) => {
    const type = Object.keys(message);
    const commonType = type[type.length - 1] || type[0];
    return (
        (!["senderKeyDistributionMessage", "messageContextInfo"].includes(
            type[0]
        ) &&
            type[0]) ||
        (type.length >= 3 && type[1] !== "messageContextInfo" && type[1]) ||
        commonType
    );
};

const serialize = (conn, m, options = {}) => {
    if (!m) return m;

    const M = proto.WebMessageInfo;
    m = M.fromObject(m);

    if (m.key) {
        const { remoteJid, participant, fromMe, id } = m.key;
        m.from = jidNormalizedUser(remoteJid || participant);
        m.fromMe = fromMe;
        m.id = id;
        m.isBot = m.id.startsWith("BAE5") && m.id.length === 16;
        m.isGroup = m.from.endsWith("@g.us");
        m.sender = jidNormalizedUser(
            (m.fromMe && conn.user?.id) || participant || m.from || ""
        );
    }

    if (m.message) {
        m.type = getTypeMessage(m.message);
        m.msg =
            m.type === "viewOnceMessage"
                ? m.message[m.type].message[
                      getTypeMessage(m.message[m.type].message)
                  ]
                : m.message[m.type];
        m.message = extractMessageContent(m.message);
        m.mentions = m.msg?.contextInfo ? m.msg.contextInfo.mentionedJid : [];
        m.quoted = m.msg?.contextInfo ? m.msg.contextInfo.quotedMessage : null;

        if (m.quoted) {
            const { contextInfo } = m.msg;
            const { quotedMessage, participant, stanzaId } = contextInfo;
            m.quoted.type = getTypeMessage(quotedMessage);
            m.quoted.msg = quotedMessage[m.quoted.type];
            m.quoted.mentions = contextInfo.mentionedJid;
            m.quoted.id = stanzaId;
            m.quoted.sender = jidNormalizedUser(participant || m.sender);
            m.quoted.from = m.from;
            m.quoted.isGroup = m.quoted.from.endsWith("@g.us");
            m.quoted.isBot =
                m.quoted.id.startsWith("BAE5") && m.quoted.id.length === 16;
            m.quoted.fromMe =
                m.quoted.sender === jidNormalizedUser(conn.user?.id);
            m.quoted.text =
                m.quoted.msg?.text ||
                m.quoted.msg?.caption ||
                m.quoted.msg?.conversation ||
                m.quoted.msg?.contentText ||
                m.quoted.msg?.selectedDisplayText ||
                m.quoted.msg?.title ||
                "";

            const vM = (m.quoted.fakeObj = M.fromObject({
                key: {
                    remoteJid: m.quoted.from,
                    fromMe: m.quoted.fromMe,
                    id: m.quoted.id,
                },
                message: m.quoted,
                ...(m.quoted.isGroup ? { participant: m.quoted.sender } : {}),
            }));

            m.quoted.delete = () =>
                conn.sendMessage(m.quoted.from, { delete: vM.key });
            m.quoted.download = (pathFile) =>
                conn.downloadMediaMessage(m.quoted.msg, pathFile);
        }
    }

    m.download = (pathFile) => conn.downloadMediaMessage(m.msg, pathFile);
    m.body = m.text =
        m.message?.conversation ||
        m.message?.[m.type]?.text ||
        m.message?.[m.type]?.caption ||
        m.message?.[m.type]?.contentText ||
        m.message?.[m.type]?.selectedDisplayText ||
        m.message?.[m.type]?.title ||
        "";
    m.reply = (text, chatId = m.from, options = {}) =>
        Buffer.isBuffer(text)
            ? conn.sendFile(chatId, text, "file", "", m, { ...options })
            : conn.sendText(chatId, text, m, { ...options });

    return m;
};

module.exports = serialize;
