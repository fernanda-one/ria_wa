const { b_sessions } = require('./blueprint.js');
const { sequelize } = require('../config/database.js');

const table = sequelize.define(...b_sessions());

class SessionsDatabase {
    constructor() {
        this.table = table;
    }

    async findSessionId(id) {
        return await this.table.findOne({ where: { id: id } });
    }

    async findApiKey(api_key) {
        return await this.table.findOne({ where: { api_key: api_key } });
    }

    async updateStatus(id, status = 'STOPPED', whatsapp_number = null) {
        return await this.table.update({ status: status, whatsapp_number: whatsapp_number }, { where: { id: id } });
    }

    async getWebhook(id) {
        let session = await this.table.findOne({ where: { id: id } });
        if (session) {
            return session.webhook;
        } else {
            return null;
        }
    }
}

module.exports = SessionsDatabase;