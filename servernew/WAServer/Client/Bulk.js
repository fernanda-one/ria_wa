const Client = require("./Client.js");
const eventEmitter = require("../../app/lib/Event.js");
const CampaignsDatabase = require("../../app/database/campaigns.db.js");
const BulksDatabase = require("../../app/database/bulks.db.js");

class Bulk extends CampaignsDatabase {
    constructor(sessionId, session) {
        super();
        this.sessionId = sessionId;
        this.session = session;
        this.status = "waiting";
        this.campaignsRunning = "none";
        this.currentCampaign = null;
        this.bulkdb = new BulksDatabase();
    }

    async mainHandler() {
        eventEmitter.on("wa.connection", async ({ session_id, status }) => {
            if (session_id !== this.session) return;
            this.status = status;
            if (status === "open") {
                return this.set_cron_schedule();
            } else {
                this.campaignsRunning = "none";
            }
        });

        eventEmitter.on("wacon", async (session) => {
            if (session !== this.sessionId) return;
            return this.set_cron_schedule();
        });
    }

    async set_cron_schedule() {
        this.currentCampaign = await this.getCampaignsOn(this.sessionId);
        if (this.currentCampaign === null) {
            this.campaignsRunning = "none";
            return;
        }
        if (this.campaignsRunning === this.currentCampaign.id) return;
        this.campaignsRunning = this.currentCampaign.id;
        let scheduledTime = new Date(
            this.currentCampaign.scheduled_at
        ).getTime();
        let intervalId = setInterval(async () => {
            if (this.campaignsRunning === "none")
                return clearInterval(intervalId);
            if (this.status === "waiting") {
                this.campaignsRunning = "none";
                return clearInterval(intervalId);
            }
            if (new Date().getTime() >= scheduledTime) {
                await this.processBulkMessages();
                return clearInterval(intervalId);
            }
            await new Promise((resolve) => setTimeout(resolve, 5000));
        }, 10000);
    }

    async processBulkMessages() {
        try {
            let bulkData = await this.bulkdb.getBulk(this.currentCampaign.id);
            if (bulkData.length === 0) {
                this.campaignsRunning = "none";
                await this.updateCampaign(this.currentCampaign.id, "completed");
                return;
            }
            let delay = this.currentCampaign.delay * 1000;
            if (this.currentCampaign.message_type === "text") {
                await this.updateCampaign(
                    this.currentCampaign.id,
                    "processing"
                );
            }
            for (let i = 0; i < bulkData.length; i++) {
                let row = bulkData[i];
                if (this.campaignsRunning === "none") break;
                if (this.status === "waiting") {
                    this.campaignsRunning = "none";
                    break;
                }
                if (this.campaignsRunning !== row.campaign_id) {
                    this.campaignsRunning = "none";
                    break;
                }
                let client = new Client(this.sessionId, row.receiver);
                if (!(await client.isWhatsapp(row.receiver))) {
                    await this.bulkdb.updateBulk(row.id, "invalid");
                    continue;
                }
                await this.sendMessage({
                    client,
                    row,
                    message: this.currentCampaign.message,
                });
                await new Promise((resolve) => setTimeout(resolve, delay));
            }
        } catch (error) {
            console.error(error);
        }
    }

    async sendMessage({ client, row, message }) {
        let msgContent = JSON.parse(row.message);
        switch (row.message_type) {
            case "text":
                client
                    .sendText(msgContent.text)
                    .then(() => {
                        this.bulkdb.updateBulk(row.id, "sent");
                    })
                    .catch(() => {
                        this.bulkdb.updateBulk(row.id, "failed");
                    });
                break;
            case "media":
                let mediaOptions = {
                    file: { mimetype: "" + msgContent.media_type },
                };
                client
                    .sendMedia(msgContent.url, msgContent.caption, mediaOptions)
                    .then(() => {
                        this.bulkdb.updateBulk(row.id, "sent");
                    })
                    .catch(() => {
                        this.bulkdb.updateBulk(row.id, "failed");
                    });
                break;
            case "button":
                client
                    .sendButton({
                        text: msgContent.text,
                        footer: msgContent.footer,
                        buttons: msgContent.buttons,
                    })
                    .then(() => {
                        this.bulkdb.updateBulk(row.id, "sent");
                    })
                    .catch(() => {
                        this.bulkdb.updateBulk(row.id, "failed");
                    });
                break;
            case "list":
                if (row.receiver.includes("@g.us")) {
                    this.bulkdb.updateBulk(row.id, "invalid");
                    return;
                }
                client
                    .sendListButton({
                        title: msgContent.title,
                        text: msgContent.text,
                        footer: msgContent.footer,
                        button_text: msgContent.buttonText,
                        sections: msgContent.sections,
                    })
                    .then(() => {
                        this.bulkdb.updateBulk(row.id, "sent");
                    })
                    .catch(() => {
                        this.bulkdb.updateBulk(row.id, "failed");
                    });
                break;
        }
    }
}

module.exports = Bulk;
