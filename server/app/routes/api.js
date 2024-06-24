const express = require('express');
const ApiController = require('../controllers/api.controller.js');
const Validator = require('../lib/Validator.js');

const router = express.Router();
const api = new ApiController();
const validator = new Validator();

router.get('/', api.index.bind(api));

router.post('/send-message', validator.sendMessage(), api.sendMessage.bind(api));
router.post('/send-media', validator.sendMedia(), api.sendMedia.bind(api));
router.post('/send-button', validator.sendButton(), api.sendButton.bind(api));
router.post('/send-listmsg', validator.sendListButton(), api.sendListButton.bind(api));

router.post('/fetch-group', validator.getGroup(), api.fetchGroup.bind(api));
router.post('/fetch-group-members', validator.fetchGroupMembers(), api.fetchGroupMembers.bind(api));

router.post('/get-plugins', validator.getPlugins(), api.getPlugins.bind(api));
router.post('/act-plugins', validator.actPlugins(), api.actPlugin.bind(api));
router.get('/reload-plugins', api.reloadPlugins.bind(api));

router.post('/triger-campaigns', validator.trigerCampaigns(), api.trigerCampaigns.bind(api));
module.exports = router;
