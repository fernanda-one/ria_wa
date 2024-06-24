const { body } = require('express-validator');

class Validator {
    constructor() {
    }

    sendMessage() {
        return [
            body('api_key').exists().withMessage('API key is required'),
            body('receiver').exists().withMessage('receiver is required'),
            body('data').exists().withMessage('data is required').isObject().withMessage('data must be an object ex: { message: "Hello World!" }'),
            body('data.message').exists().withMessage('message is required'),
        ];
    }

    sendMedia() {
        return [
            body('api_key').exists().withMessage('API key is required'),
            body('receiver').exists().withMessage('receiver is required'),
            body('data').exists().withMessage('data is required').isObject().withMessage('data must be an object.'),
            body('data.url').exists().withMessage('url is required'),
            body('data.media_type').exists().withMessage('Media Type is required.').isIn(['image', 'video', 'audio', 'file']).withMessage('Media Type must be image, video, audio or file.')
        ]
    }

    sendButton() {
        return [
            body('api_key').exists().withMessage('API key is required'),
            body('receiver').exists().withMessage('receiver is required'),
            body('data').exists().withMessage('data is required').isObject().withMessage('data must be an object.'),
            body('data.message').exists().withMessage('message is required'),
            body('data.footer').exists().withMessage('footer is required'),
        ]
    }

    sendListButton() {
        return [
            body('api_key').exists().withMessage('API key is required'),
            body('receiver').exists().withMessage('receiver is required'),
            body('data').exists().withMessage('data is required').isObject().withMessage('data must be an object.'),
            body('data.title').exists().withMessage('title is required'),
            body('data.message').exists().withMessage('message is required'),
            body('data.footer').exists().withMessage('footer is required'),
            body('data.buttonText').exists().withMessage('buttonText is required'),
            body('data.sections').exists().withMessage('sections is required'),

        ]
    }

    getGroup() {
        return [
            body('api_key').exists().withMessage('API key is required'),
        ]
    }

    fetchGroupMembers() {
        return [
            body('api_key').exists().withMessage('API key is required'),
            body('id').exists().withMessage('ID is required'),
        ]
    }

    trigerCampaigns() {
        return [
            body('api_key').exists().withMessage('API key is required')
        ]
    }

    getPlugins() {
        return [
            body('api_key').exists().withMessage('API key is required')
        ]
    }

    actPlugins() {
        return [
            body('api_key').exists().withMessage('API key is required'),
            body('status').exists().withMessage('status is required').isIn(['active', 'inactive']).withMessage('status must be active or inactive'),
            body('commands_name').exists().withMessage('commands_name is required')
        ]
    }
}

module.exports = Validator;
