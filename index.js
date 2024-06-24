require("dotenv").config();
process.env.TZ = process.env.APP_TIMEZONE || "Asia/Jakarta";

const app = require("./server/app/config/app.js")
const { logger } = require("./server/app/lib/myf.velixs.js");
const { Server } = require('socket.io');
const SessionsDatabase = require("./server/app/database/sessions.db.js");
const SessionConnection = require("./server/WAServer/session.js");
const url = require('url');

const server = new app();

const { hostname, port } = url.parse(process.env.BASE_NODE);

const serverHttp = server.app.listen(port || 5570, hostname, () => {
    logger("info", `[EXPRESS] App Listening at ${process.env.BASE_NODE}`);
});

server.app.get("/", (req, res) => {
    res.redirect(process.env.BASE_NODE + '/app');
});

const io = new Server(serverHttp)

const socket = io.on("connection", (socket) => {
    socket.on("getSession", async (session) => {
        let get_dbsession = await new SessionsDatabase().findSessionId(session);
        let get_server_session = await new SessionConnection(socket).getSession(session);
        // check server session exists or connected
        if (get_server_session) {
            // if db session not found delete server session
            if (!get_dbsession) {
                await new SessionConnection(socket).deleteSession(session);
                return socket.emit("servervelixs", { status: false, code_message: "dbsession404", session_id: session })
            }
            if (get_dbsession.status == 'STOPPED') {
                await new SessionConnection(socket).createSession(session)
            } else {
                socket.emit(`logger`, {
                    session_id: session,
                    type: 'debug',
                    message: `[SESSION] GET SESSION.`
                })
                try {
                    socket.emit(`servervelixs`, {
                        status: true,
                        code_message: "sessionconnected",
                        session_id: session,
                        session: {
                            name: get_server_session.authState.creds.me.name,
                            number: get_server_session.authState.creds.me.id.split(":")[0],
                            platform: get_server_session.authState.creds.platform,
                        }
                    });
                } catch (e) {
                    await new SessionsDatabase().updateStatus(session);
                    // socket emit
                    socket.emit(`logger`, {
                        session_id: session,
                        type: 'error',
                        message: `[SESSION] GET SESSION FAILED, SESSION NOT FOUND.`
                    })
                    socket.emit(`servervelixs`, {
                        status: false,
                        code_message: "session404",
                        session_id: session,
                        message: 'SESSION NOT FOUND.'
                    });
                }
            }
        } else {
            if (!get_dbsession) return socket.emit("servervelixs", { status: false, code_message: "dbsession404", session_id: session })
            await new SessionConnection(socket).createSession(session)
        }
    });

    socket.on("logout", async (session) => {
        let velixs = await new SessionConnection(socket).getSession(session);
        if (velixs) {
            velixs.logout();
            velixs.ev.removeAllListeners("connection.update");
            velixs.end();
            await new SessionConnection(socket).deleteSession(session);
            await new SessionsDatabase().updateStatus(session);
            socket.emit(`servervelixs`, {
                status: true,
                code_message: "logout",
                session_id: session,
                message: "LOGOUT SUCCESS."
            });
        } else {
            socket.emit(`logger`, {
                session_id: session,
                type: 'error',
                message: `[SESSION] LOGOUT FAILED, SESSION NOT FOUND.`
            })
            socket.emit(`servervelixs`, {
                status: false,
                session_id: session,
                code_message: "logout",
                message: 'LOGOUT FAILED, SESSION NOT FOUND.'
            });
        }
    });

    return socket;
});

server.app.set('socket', socket);

(async () => {
    await new SessionConnection(socket).autoStart();
})();
