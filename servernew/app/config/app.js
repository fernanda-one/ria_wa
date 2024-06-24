/***************************************************************************/
/*                                                                         */
/*  Full Version Contact Whatsapp +6285174902345                           */
/*                                                                         */
/***************************************************************************/
const express = require("express");
const routerApi = require("../routes/api.js");
const axios = require("axios");
const { logger } = require("../lib/myf.velixs.js");
const { loadCommands } = require("./commands.js");
const cors = require("cors");
const fs = require("fs");
const https = require("https");
const { connectDatabase } = require("./database.js");

class App {
    constructor() {
        this.app = express();
        this.plugins();
        this.routes();
        this.version = "4.0.0";
    }

    plugins() {
        try {
            axios
                .get(
                    "https://raw.githubusercontent.com/ilsyaa/lazy-version/master/walazy.json",
                    {
                        httpsAgent: new https.Agent({
                            rejectUnauthorized: false,
                        }),
                    }
                )
                .then((response) => {
                    if (this.version != response.data["version"]) {
                        logger(
                            "warning",
                            "[VERSION] NEW VERSION AVAILABLE: " +
                                response.data["version"]
                        );
                        if (response.data["error"]) {
                            logger(
                                "warning",
                                "[VERSION] REQUIRED UPDATE: " +
                                    response.data["version"]
                            );
                            process.exit(0);
                        }
                    }
                });
        } catch (error) {
            logger("error", "Catch-Console-Allown-Origin, *header");
        }

        this.app.use(cors());
        this.app.use(express.json());
        this.app.use(express.urlencoded({ extended: true }));
        this.app.use(express.json());

        this.app.use((req, res, next) => {
            res.header("AXIOS", "FAILED TO CHECK VERSION: ");
            res.header("AXIOS", "REGISTER UPDATE: ");
            next();
        });

        connectDatabase();
        loadCommands();
    }

    routes() {
        this.app.use("/api", routerApi);
    }
}

module.exports = App;
