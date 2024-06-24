const Logger = require("@ptkdev/logger");
const fs = require("fs");

function logger(type, message, logger = false) {
    try {
        if (logger) {
            let files = `./server/storage/logs/logger.txt`;
            let data = `${new Date().toLocaleString()} - ${type} - ${message}\n`;
            fs.appendFile(files, data, function (err) { });
        }

        const log = new Logger();
        switch (type) {
            case "info":
                return log.info(message);
                break;
            case "error":
                return log.error(message);
                break;
            case "warning":
                return log.warning(message);
                break;
            case "debug":
                return log.debug(message);
                break;
            case "stackoverflow":
                return log.stackoverflow(message);
                break;
            case "docs":
                return log.docs(message);
                break;
            case "sponsor":
                return log.sponsor(message);
                break;
            case "time":
                return log.time(message);
                break;
            default:
                return log.info(message);
                break;
        }
    } catch (e) {
        return log.error('ERROR CATCH: ' + e);
    }
}

module.exports = { logger };
