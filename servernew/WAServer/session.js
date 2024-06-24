const {
    default: WASocket,
    makeInMemoryStore,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestWaWebVersion,
} = require("@whiskeysockets/baileys");
const { logger } = require("../app/lib/myf.velixs.js");
const pino = require("pino");
const qrcode = require("qrcode");
const fs = require("fs");
const SessionsDatabase = require("../app/database/sessions.db.js");
const { Boom } = require("@hapi/boom");
const Message = require("./Client/MessageHandler.js");
const Bulk = require("./Client/Bulk.js");
const eventEmitter = require("../app/lib/myf.velixs.js");

let sessionMap = new Map();

class SessionConnection extends SessionsDatabase {
    constructor(socket) {
        super();
        this.socket = socket;
        this.sessionPath = __dirname + "/../storage/sessions";
        this.storagePath = this.sessionPath + "/store_walix.json";
        this.timeOutQr = 0;
    }

    async getSession(sessionId) {
        return sessionMap.get(sessionId) ? sessionMap.get(sessionId) : null;
    }

    async deleteSession(sessionId) {
        try {
            sessionMap.delete(sessionId);
            if (fs.existsSync(`${this.sessionPath}/${sessionId}`)) {
                fs.rmSync(`${this.sessionPath}/${sessionId}`, {
                    force: true,
                    recursive: true,
                });
            }
            logger("info", `[SESSION] SESSION DELETED: ${sessionId}`);
        } catch (error) {
            logger("error", `[SESSION] SESSION DELETED ERROR: ${sessionId}`);
        }
    }

    async generateQrCode(qr, sessionId) {
        const qrCodeUrl = await qrcode.toDataURL(qr, { scale: 8 });
        setTimeout(() => {
            this.socket.emit("qr200", {
                status: true,
                code_message: "QR Code Expired",
                session_id: sessionId,
                qr: qrCodeUrl,
            });
        }, 2000);

        this.timeOutQr++;
        logger("info", `[SESSION] WAITING FOR THE SCAN QR (${this.timeOutQr})`);
        this.socket.emit("logger", {
            session_id: sessionId,
            type: "qr",
            message: `[SESSION] WAITING FOR THE SCAN QR: ${this.timeOutQr} OF ${process.env.TIME_OUT_QR}).`,
        });
    }

    async autoStart() {
        const activeSessions = await this.table.findAll({
            where: { status: "CONNECTED" },
        });
        if (activeSessions.length > 0) {
            activeSessions.forEach(async (session) => {
                if (fs.existsSync(`${this.sessionPath}/${session.id}`)) {
                    logger(
                        "info",
                        `[SESSION] AUTO START: ${session.session_name}`
                    );

                    await this.createSession(session.id);
                } else {
                    logger(
                        "warning",
                        `[SESSION] AUTO START ERROR: ${session.session_name}`
                    );
                    await this.deleteSession(session.id);
                }
            });
        }
    }

    async createSession(sessionId) {
        const sessionDir = `${this.sessionPath}/${sessionId}`;
        const storageFilePath = `${this.storagePath}/${sessionId}_store.json`;

        if (!fs.existsSync(sessionDir)) {
            fs.mkdirSync(sessionDir, { recursive: true });
        }

        const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
        const { version, isLatest } = await fetchLatestWaWebVersion();
        const store = makeInMemoryStore({
            logger: pino().child({ level: "silent", stream: "store" }),
        });

        const socket = WASocket({
            printQRInTerminal: false,
            auth: state,
            logger: pino({ level: "silent" }),
            browser: ["Safari", "Safari", "3.0"],
            version,
        });

        store.readFromFile(storageFilePath);
        const writeStoreToFileInterval = setInterval(() => {
            try {
                store.writeToFile(storageFilePath);
            } catch (error) {
                if (error.code === "ENOENT")
                    clearInterval(writeStoreToFileInterval);
            }
        }, 10000);

        store.bind(socket.ev);
        socket.chats = store.chats;

        sessionMap.set(sessionId, { ...socket, isStop: false });

        socket.ev.on("creds.update", saveCreds);
        socket.ev.on("connection.update", async (update) => {
            const { connection, qr, lastDisconnect, isNewLogin } = update;

            if (isNewLogin) {
                try {
                    if (await this.getSession(sessionId)) {
                        await this.updateStatus(
                            sessionId,
                            "CONNECTED",
                            socket.authState.creds.me.id.split(":")[0]
                        );
                    } else {
                        this.handleDeviceNotFound(sessionId, socket);
                    }
                    this.handleNewConnection(sessionId, socket);
                } catch (error) {
                    logger(
                        "error",
                        `[SESSION] NEW CONNECTED ERROR: ${sessionId}`
                    );
                }
            } else if (qr) {
                this.handleQrUpdate(sessionId, qr, socket);
            } else {
                this.handleConnectionUpdate(
                    sessionId,
                    connection,
                    lastDisconnect,
                    socket
                );
            }
        });
    }

    handleDeviceNotFound(sessionId, socket) {
        this.socket.emit("logger", {
            session_id: sessionId,
            type: "error",
            message: "DEVICE NOT FOUND.",
        });
        this.socket.emit("qr200", {
            status: true,
            code_message: "device404",
            session_id: sessionId,
            message: "DEVICE NOT FOUND, PLEASE REFRESH PAGE.",
        });
        socket.ev.removeAllListeners("connection.update");
        socket.end();
        this.deleteSession(sessionId);
    }

    handleNewConnection(sessionId, socket) {
        this.socket.emit("logger", {
            session_id: sessionId,
            type: "connected",
            message: "[SESSION] NEW CONNECTED.",
        });
        this.socket.emit("qr200", {
            status: true,
            code_message: "CONNECTED",
            session_id: sessionId,
            session: {
                name: socket.authState.creds.me.name,
                number: socket.authState.creds.me.id.split(":")[0],
                platform: socket.authState.creds.platform,
                log: "",
            },
        });
        eventEmitter.emit("wa.connection", {
            session_id: sessionId,
            status: "open",
        });
    }

    handleQrUpdate(sessionId, qr, socket) {
        try {
            if (this.timeOutQr >= process.env.TIME_OUT_QR) {
                socket.ev.removeAllListeners("connection.update");
                this.deleteSession(sessionId);
                logger(
                    "error",
                    `[SESSION] LOGGED OUT, PLEASE REGENERATE QR CODE: ${sessionId}`
                );
                this.socket.emit("logger", {
                    session_id: sessionId,
                    type: "error",
                    message: "Logged Out, Please Regenerate QR Code.",
                });
                this.socket.emit("qr200", {
                    status: true,
                    code_message: "regenerateqr",
                    session_id: sessionId,
                    message: "QR Code Expired",
                });
                return;
            }
            this.generateQrCode(qr, sessionId);
        } catch (error) {
            logger("error", `[SESSION] QR UPDATE ERROR: ${sessionId}`);
        }
    }

    handleConnectionUpdate(sessionId, connection, lastDisconnect, socket) {
        const { code: statusCode } = new Boom(lastDisconnect?.error)?.output
            .statusCode;
        switch (connection) {
            case "close":
                this.handleCloseConnection(sessionId, statusCode, socket);
                break;
            case "connecting":
                logger("info", `[SESSION] CONNECTING: ${sessionId}`);
                break;
            case "open":
                logger("info", `[SESSION] OPEN: ${sessionId}`);
                break;
            default:
                logger(
                    "info",
                    `[SESSION] UNKNOWN CONNECTION STATUS: ${connection}`
                );
        }
    }

    handleCloseConnection(sessionId, statusCode, socket) {
        this.deleteSession(sessionId);
        eventEmitter.emit("sessionconnected", {
            session_id: sessionId,
            status: "close",
        });

        switch (statusCode) {
            case DisconnectReason.badSession:
                this.handleBadSession(sessionId, socket);
                break;
            case DisconnectReason.connectionClosed:
                this.handleConnectionClosed(sessionId, socket);
                break;
            case DisconnectReason.connectionLost:
                this.handleConnectionLost(sessionId, socket);
                break;
            case DisconnectReason.connectionReplaced:
                this.handleConnectionReplaced(sessionId, socket);
                break;
            default:
                this.handleUnknownDisconnection(sessionId, socket);
        }
    }

    handleBadSession(sessionId, socket) {
        this.socket.emit("qr200", {
            code_message: "endsession",
            session_id: sessionId,
            message: "Bad Session File.",
        });
        this.socket.emit("logger", {
            session_id: sessionId,
            type: "error",
            message: "[SESSION] BAD SESSION FILE, PLEASE REGENERATE QR CODE.",
        });
        logger("error", `[SESSION] BAD SESSION FILE: ${sessionId}`);
        socket.ev.removeAllListeners("connection.update");
        socket.end();
        this.deleteSession(sessionId);
    }

    handleConnectionClosed(sessionId, socket) {
        logger(
            "info",
            `[SESSION] CONNECTION CLOSED, RECONNECTING: ${sessionId}`
        );
        this.socket.emit("servervelixs", {
            code_message: "connectionClosed",
            session_id: sessionId,
            message: "Connection Closed, Reconnecting...",
        });
        this.createSession(sessionId);
    }

    handleConnectionLost(sessionId, socket) {
        logger("info", `[SESSION] CONNECTION LOST, RECONNECTING: ${sessionId}`);
        this.socket.emit("servervelixs", {
            code_message: "connectionLost",
            session_id: sessionId,
            message: "Connection Lost, Reconnecting...",
        });
        this.createSession(sessionId);
    }

    handleConnectionReplaced(sessionId, socket) {
        logger(
            "error",
            `[SESSION] CONNECTION REPLACED, YOU'RE LOGGED OUT: ${sessionId}`
        );
        this.socket.emit("servervelixs", {
            code_message: "connectionReplaced",
            session_id: sessionId,
            message: "Connection Replaced, You're Logged Out.",
        });
        socket.ev.removeAllListeners("connection.update");
        socket.end();
        this.deleteSession(sessionId);
    }

    handleUnknownDisconnection(sessionId, socket) {
        logger("error", `[SESSION] UNKNOWN DISCONNECTION: ${sessionId}`);
        socket.ev.removeAllListeners("connection.update");
        socket.end();
        this.deleteSession(sessionId);
    }
}

module.exports = SessionConnection;
