const EventEmitter = require("events");
const eventEmitter = new EventEmitter();
eventEmitter.setMaxListeners(0)

module.exports = eventEmitter;
