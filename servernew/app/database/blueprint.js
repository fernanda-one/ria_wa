const { DataTypes } = require('sequelize');

module.exports = {
    b_sessions() {
        return [
            'sessions',
            {
                id: {
                    type: DataTypes.UUID,
                    defaultValue: DataTypes.UUIDV4,
                    primaryKey: true,
                    allowNull: false,
                },
                session_name: {
                    type: DataTypes.STRING,
                    allowNull: false,
                },
                whatsapp_number: {
                    type: DataTypes.STRING,
                    allowNull: true,
                },
                user_id: {
                    type: DataTypes.BIGINT,
                    allowNull: false,
                },
                status: {
                    type: DataTypes.ENUM('CONNECTED', 'STOPPED'),
                    allowNull: false,
                    defaultValue: 'STOPPED',
                },
                webhook: {
                    type: DataTypes.TEXT,
                    allowNull: true,
                },
                api_key: {
                    type: DataTypes.STRING,
                    allowNull: false,
                    unique: true,
                }
            },
            { tableName: "sessions", timestamps: true, createdAt: "created_at", updatedAt: "updated_at" }
        ]
    },
    b_users() {
        return [
            'users',
            {
                id: {
                    type: DataTypes.BIGINT,
                    autoIncrement: true,
                    primaryKey: true,
                    allowNull: false,
                },
                name: {
                    type: DataTypes.STRING,
                    allowNull: false,
                },
                username: {
                    type: DataTypes.STRING,
                    allowNull: false,
                    unique: true,
                },
                role: {
                    type: DataTypes.ENUM('admin', 'user'),
                    allowNull: false,
                    defaultValue: 'user',
                },
                password: {
                    type: DataTypes.STRING,
                    allowNull: false,
                },
                remember_token: {
                    type: DataTypes.STRING(100),
                    allowNull: true,
                }
            },
            { tableName: "users", timestamps: true, createdAt: "created_at", updatedAt: "updated_at" }
        ]
    },
    b_responders() {
        return [
            'auto_responders',
            {
                id: {
                    type: DataTypes.BIGINT,
                    autoIncrement: true,
                    primaryKey: true,
                    allowNull: false,
                },
                user_id: {
                    type: DataTypes.BIGINT,
                    allowNull: false,
                },
                session_id: {
                    type: DataTypes.UUID,
                    allowNull: false,
                },
                keyword: {
                    type: DataTypes.STRING,
                    allowNull: false,
                },
                message_type: {
                    type: DataTypes.ENUM('text', 'media', 'button'),
                    allowNull: false,
                    defaultValue: 'text',
                },
                message: {
                    type: DataTypes.TEXT,
                    allowNull: false,
                },
                status: {
                    type: DataTypes.ENUM('active', 'inactive'),
                    allowNull: false,
                    defaultValue: 'active',
                },
                reply_when: {
                    type: DataTypes.ENUM('all', 'group', 'personal'),
                    allowNull: false,
                    defaultValue: 'all',
                }
            },
            { tableName: "auto_responders", timestamps: true, createdAt: "created_at", updatedAt: "updated_at" }
        ]
    },
    b_campaigns() {
        return [
            'campaigns',
            {
                id: {
                    type: DataTypes.BIGINT,
                    autoIncrement: true,
                    primaryKey: true,
                    allowNull: false,
                },
                user_id: {
                    type: DataTypes.BIGINT,
                    allowNull: false,
                },
                session_id: {
                    type: DataTypes.UUID,
                    allowNull: false,
                },
                name: {
                    type: DataTypes.STRING,
                    allowNull: false,
                },
                phonebook_id: {
                    type: DataTypes.BIGINT,
                    allowNull: false,
                },
                message_type: {
                    type: DataTypes.STRING,
                    allowNull: false,
                },
                message: {
                    type: DataTypes.TEXT,
                    allowNull: false,
                },
                status: {
                    type: DataTypes.ENUM('paused', 'completed', 'waiting', 'processing'),
                    allowNull: false,
                    defaultValue: 'waiting',
                },
                delay: {
                    type: DataTypes.INTEGER,
                    allowNull: false,
                    defaultValue: 0
                },
                scheduled_at: {
                    type: DataTypes.DATE,
                    allowNull: true,

                }
            },
            { tableName: "campaigns", timestamps: true, createdAt: "created_at", updatedAt: "updated_at" }
        ]
    },
    b_bulks() {
        return [
            'bulks',
            {
                id: {
                    type: DataTypes.BIGINT,
                    autoIncrement: true,
                    primaryKey: true,
                    allowNull: false,
                },
                user_id: {
                    type: DataTypes.BIGINT,
                    allowNull: false,
                },
                session_id: {
                    type: DataTypes.UUID,
                    allowNull: false,
                },
                campaign_id: {
                    type: DataTypes.BIGINT,
                    allowNull: false,
                },
                receiver: {
                    type: DataTypes.STRING,
                    allowNull: false,
                },
                message_type: {
                    type: DataTypes.STRING,
                    allowNull: false,
                },
                message: {
                    type: DataTypes.TEXT,
                    allowNull: false,
                },
                status: {
                    type: DataTypes.ENUM('sent', 'invalid', 'failed', 'pending'),
                    allowNull: false,
                    defaultValue: 'pending',
                },
            },
            { tableName: "bulks", timestamps: true, createdAt: "created_at", updatedAt: "updated_at" }
        ]
    },
}