const { b_users } = require('./blueprint.js');
const { sequelize } = require('../config/database.js');

const table = sequelize.define(...b_users());

class UsersDatabase {
    constructor() {
        this.table = table;
    }

    async isAdmin(id) {
        await this.table.findOne({ where: { id: id } }).then((result) => {
            if (result) {
                if (result.dataValues.role == 'admin') {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }).catch((err) => {
            return false;
        });
    }

}

module.exports = UsersDatabase;
