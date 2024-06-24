const { b_campaigns } = require('./blueprint.js');
const { sequelize } = require('../config/database.js');
const { Op } = require('sequelize');

const table = sequelize.define(...b_campaigns());

class CampaignsDatabase {
    constructor() {
        this.table = table;
    }

    async getCampaignsOn(session) {
        return await this.table.findOne({
            where: {
                session_id: session,
                [Op.or]: [
                    { status: 'waiting' },
                    { status: 'processing' }
                ]
            }
        })
    }

    async updateCampaign(id, status) {
        return await this.table.update({ status: status }, { where: { id: id } });
    }
}

module.exports = CampaignsDatabase;