const { b_bulks } = require('./blueprint.js');
const { sequelize } = require('../config/database.js');
const { Op } = require('sequelize');

const table = sequelize.define(...b_bulks());

class BulksDatabase {
    constructor() {
        this.table = table;
    }

    async getBulk(campaign_id) {
        return await this.table.findAll({
            where: {
                campaign_id,
                [Op.or]: [
                    { status: 'pending' },
                    { status: 'failed' }
                ]
            }
        });
    }

    async updateBulk(bulk_id, status) {
        return await this.table.update({ status }, {
            where: {
                id: bulk_id
            }
        });
    }
}

module.exports = BulksDatabase;