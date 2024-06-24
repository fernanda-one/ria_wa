class IlsyaClient {
    constructor(socket) {
        this.session = `${$('meta[name="device_id"]').attr('content')}`
        this.device_status = `${$('meta[name="device_status"]').attr('content')}`
        this.content = $('#digidaw-velixs')
        this.content_detail = $('#content-detail')
        this.btn_logout = $(".is-logout")
        this.logger = $('#logger')
        this.limit_logger = 5
        this.attemptNumber = 0
        this.socket = socket
    }


    init() {
        if (this.device_status == 'CONNECTED') {
            console.log('auto start');
            this.startSession()
        }

        // emit from server
        this.socket.on('servervelixs', (res) => {
            if (res.session_id == this.session) {
                switch (res.code_message) {
                    case 'qr200':
                        this.content.html(`<div class="d-block"><div class="d-flex justify-content-center"><img style="max-height: 100%; max-width: 100%; height: 17rem; width: 17rem" src="${res.qr}" alt="qr"></div><div class="d-block" style="padding-top:40px"><div class="text-muted">SCAN WITH YOUR WHATSAPP ACCOUNT.</div></div></div>`);
                        break;
                    case 'regenerateqr':
                        this.content.html(`<div class="d-block"><div class="d-flex justify-content-center"><div class="sk-fold sk-secondary"><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div></div></div><div class="d-block" style="padding-top:40px"><div class="text-muted" id="status-waiting">QR EXPIRED</div></div></div><div class="d-block"><div class="text-center" style="position: absolute; right: 0; bottom: 30px; left: 0;"><button class="btn btn-primary startbutton">REGENERATE QR</button></div></div>`);
                        break
                    case 'sessionconnected':
                        this.content.html(`<div class="d-block"><div class="text-center mb-3 text-muted"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg></div><div class="d-block"><div class="text-muted" id="status-waiting">SESSION CONNECTED</div></div></div>`);
                        this.content_detail.html(`<li class="mb-2"><span class="fw-semibold me-1">Whatsapp Name :</span><span>${res.session.name}</span></li><li class="mb-2"><span class="fw-semibold me-1">Whatsapp Number :</span><span>${res.session.number}</span></li>`)
                        break
                    case 'endsession':
                        this.content.html(`<div class="d-block"><div class="text-center mb-3 text-muted"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7.174 3.178c.252 -.114 .531 -.178 .826 -.178h8a2 2 0 0 1 2 2v9m0 4v1a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-13"></path><path d="M11 4h2"></path><path d="M12 17v.01"></path><path d="M3 3l18 18"></path></svg></div><div class="d-block"><div class="text-muted" id="status-waiting">${res.nessage}</div></div></div>`);
                        this.restart_content_detail()
                        break
                    case 'logout':
                        if (res.status) {
                            this.content.html(`<div class="d-block"><div class="d-block"><div class="text-muted" id="status-waiting">${res.message}</div></div></div><div class="d-block"><div class="text-center" style="position: absolute; right: 0; bottom: 30px; left: 0;"><button class="btn btn-primary startbutton">RESTART SESSION</button></div></div>`)
                            this.restart_content_detail()
                        } else {
                            this.content.html(`<div class="d-block"><div class="d-block"><div class="text-muted" id="status-waiting">${res.message}</div></div></div><div class="d-block"><div class="text-center" style="position: absolute; right: 0; bottom: 30px; left: 0;"><button class="btn btn-primary startbutton">REFRESH SESSION</button></div></div>`)
                        }
                        setTimeout(() => {
                            this.btn_logout.attr('disabled', false)
                            this.btn_logout.html('Log out')
                        }, 3000);
                        break
                    default:
                        // if (res.message) {
                        //     msg = res.message
                        // } else {
                        //     msg = 'Something went wrong'
                        // }
                        this.content.html(`<div class="d-block"><div class="d-block"><div class="text-muted" id="status-waiting">....</div></div></div><div class="d-block"><div class="text-center" style="position: absolute; right: 0; bottom: 30px; left: 0;"><button class="btn btn-primary refresh-page">REFRESH PAGE</button></div></div>`)
                        break
                }
            }
        })

        // loger
        this.socket.on('logger', (res) => {
            if (res.session_id == this.session) {
                this.show_logger(res)
            }
        })


        this.socket.on('connect', () => {
            if (this.device_status == 'CONNECTED') {
                this.content.html(`<div class="d-block"><div class="d-flex justify-content-center"><div class="sk-fold sk-secondary"><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div></div></div><div class="d-block" style="padding-top:40px"><div class="text-muted" id="status-waiting">WAITING FOR SERVER RESPONSE</div></div></div>`)
            } else {
                this.content.html(`<div class="d-block"><div class="d-block"><div class="text-muted" id="status-waiting">CLICK START SESSION !</div></div></div><div class="d-block"><div class="text-center" style="position: absolute; right: 0; bottom: 30px; left: 0;"><button class="btn btn-primary startbutton">START SESSION</button></div></div>`)
            }
            this.show_logger({
                type: 'info',
                message: '[SERVER] CONNECTED TO SERVER.'
            })
            this.attemptNumber = 0
        });


        this.socket.on('connect_error', () => {
            this.content.html(`<div class="d-block"><div class="text-center mb-3 text-muted"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 18l.01 0"></path><path d="M9.172 15.172a4 4 0 0 1 5.656 0"></path><path d="M6.343 12.343a7.963 7.963 0 0 1 3.864 -2.14m4.163 .155a7.965 7.965 0 0 1 3.287 2"></path><path d="M3.515 9.515a12 12 0 0 1 3.544 -2.455m3.101 -.92a12 12 0 0 1 10.325 3.374"></path><path d="M3 3l18 18"></path></svg></div><div class="d-block"><div class="text-muted" id="status-waiting">SERVER OFFLINE</div></div></div>`)
            this.restart_content_detail()
            this.attemptNumber++;
            if (this.attemptNumber >= 10) {
                this.show_logger({
                    type: 'error',
                    message: `[SERVER] SERVER NOT RESPONDING, PLESE RELOAD PAGE.`
                })
            } else {
                this.show_logger({
                    type: 'error',
                    message: `[SERVER] SERVER OFFLINE, TRY TO RECONNECT... (${this.attemptNumber} of 10)`
                })
            }
        });
    }

    startSession() {
        this.socket.emit('getSession', this.session)
    }

    show_logger(res) {
        if (this.logger.find('tr').length >= this.limit_logger) {
            this.logger.find('tr:last').remove()
        }
        switch (res.type) {
            case "info":
                this.logger.prepend(`<tr><td class="text-truncate d-flex align-items-center" style="padding: 0.35rem 1.25rem !important;"><span class="badge bg-label-info small" style="width: 7rem;"><div class="d-flex align-items-center justify-content-center"><i class="ti ti-info-circle text-info ti-xs me-1"></i><span class="small">INFO</span></div></span><span class="ms-2 small">${res.message}</span></td></tr>`);
                break
            case "debug":
                this.logger.prepend(`<tr><td class="text-truncate d-flex align-items-center" style="padding: 0.35rem 1.25rem !important;"><span class="badge bg-label-primary small" style="width: 7rem;"><div class="d-flex align-items-center justify-content-center"><i class="ti ti-prompt text-primary ti-xs me-1"></i><span class="small">DEBUG</span></div></span><span class="ms-2 small">${res.message}</span></td></tr>`)
                break
            case "error":
                this.logger.prepend(`<tr><td class="text-truncate d-flex align-items-center" style="padding: 0.35rem 1.25rem !important;"><span class="badge bg-label-danger small" style="width: 7rem;"><div class="d-flex align-items-center justify-content-center"><i class="ti ti-alert-triangle text-danger ti-xs me-1"></i><span class="small">ERROR</span></div></span><span class="ms-2 small">${res.message}</span></td></tr>`)
                break
        }
    }

    logout() {
        this.socket.emit('logout', this.session)
    }

    restart_content_detail() {
        this.content_detail.html('<li class="mb-2"><span span class= "fw-semibold me-1" > Session Name :</span ><span>-</span></li><li class="mb-2"><span class="fw-semibold me-1">Whatsapp Number :</span><span>-</span></li>')
    }
}
