const commands = new Map();
const fs = require('fs');
const { logger } = require('../lib/myf.velixs');


const loadCommands = () => {
    commands.clear();
    let dir = fs.readdirSync(__dirname + "/../../commands");
    for (let i = 0; i < dir.length; i++) {
        if (!fs.lstatSync(__dirname + "/../../commands/" + dir[i]).isDirectory()) continue;
        try {
            let file = require(`../../commands/${dir[i]}/app.js`);
            let name_key = file.name.toLowerCase().replace(/\s/g, "");
            if (!commands.has(name_key)) {
                commands.set(name_key, file);
            }
        } catch (e) {
            // console.log(e);
            logger('error', 'Failed to load command: ' + dir[i])
        }
    }
    logger('info', '[COMMANDS] Loaded ' + commands.size + ' commands')
    // console.log(Array.from(commands.values()).find((v) => v.app.cmd.find((x) => x.toLowerCase() == 'ping')));
}

const checkSessionCommands = (session, commands_name) => {
    let storagePath = __dirname + "/../../storage/sessions/" + session + "/aplugins.json";
    try {
        if (!fs.existsSync(storagePath)) {
            fs.writeFileSync(storagePath, JSON.stringify([]));
        }
        let json = fs.readFileSync(storagePath, 'utf8');
        let plugins = JSON.parse(json);
        if (plugins.length == 0) return false;
        let name_key = commands_name.toLowerCase().replace(/\s/g, "");
        for (let i = 0; i < plugins.length; i++) {
            if (plugins[i] === name_key) {
                return true;
            }
        }
        return false;
    } catch (e) {
        return false;
    }
}

const getSessionCommands = (session) => {
    let storagePath = __dirname + "/../../storage/sessions/" + session + "/aplugins.json";
    try {
        if (!fs.existsSync(storagePath)) {
            fs.writeFileSync(storagePath, JSON.stringify([]));
            return [];
        }
        let json = fs.readFileSync(storagePath, 'utf8');
        let plugins = JSON.parse(json);
        return plugins;
    } catch (e) {
        return [];
    }
}

const actSessionCommands = async (session, action, value) => {
    let storagePath = __dirname + "/../../storage/sessions/" + session + "/aplugins.json";
    try {
        if (!fs.existsSync(storagePath)) {
            fs.writeFileSync(storagePath, JSON.stringify([]));
        }
        let json = fs.readFileSync(storagePath, 'utf8');
        let plugins = JSON.parse(json);
        if (action == 'delete') {
            if (plugins.length == 0) return true;
            let newPlugins = [];
            for (let i = 0; i < plugins.length; i++) {
                if (plugins[i] !== value) {
                    newPlugins.push(plugins[i]);
                }
            }
            fs.writeFileSync(storagePath, JSON.stringify(newPlugins));
            return true;
        } else if (action == 'add') {
            let newPlugins = [];
            let duplicate = false;
            for (let i = 0; i < plugins.length; i++) {
                // duplicate check
                if (plugins[i] === value) {
                    duplicate = true;
                    break;
                }
                newPlugins.push(plugins[i]);
            }
            if (duplicate) return true;
            newPlugins.push(value);
            fs.writeFileSync(storagePath, JSON.stringify(newPlugins));
            return true;
        }
        return false;
    } catch (e) {
        return false;
    }
}

module.exports = {
    loadCommands,
    commands,
    checkSessionCommands,
    getSessionCommands,
    actSessionCommands
}
