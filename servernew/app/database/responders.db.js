const { b_responders } = require('./blueprint.js');
const { sequelize } = require('../config/database.js');

const table = sequelize.define(...b_responders());

class RespondersDatabase {
    constructor() {
        this.table = table;
    }

    async findAutoResponder({ keyword, session_id }) {
        return await this.table.findAll({
            where: {
                status: 'active',
                session_id: session_id,
                keyword: keyword,
                type_keyword: 'equal',
            }
        });
    }

    async finAll({ keyword, session_id }) {
        return await this.table.findAll({
            where: {
                status: 'active',
                session_id: session_id,
                type_keyword: 'contains',
            },
        });
    }
}

module.exports = RespondersDatabase;
