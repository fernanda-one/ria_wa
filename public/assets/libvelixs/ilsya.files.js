class FileManager {
    constructor({ subfolder, base_url }) {
        this.subfolder = subfolder ? subfolder : '';
        this.base_url = base_url;
    }

    init({ body, ismain }) {
        this.body = $(body);
        $.ajax({
            url: this.trimUrl(this.base_url),
            type: 'GET',
            data: {
                subfolder: this.subfolder,
                ismain: ismain
            },
            // loading
            beforeSend: () => {
                this.body.html('<div class="card"><nav class="navbar navbar-expand-lg bg-navbar-theme"><div class="container-fluid"><a class="navbar-brand" href="javascript:void(0)">File Manager</a><a class="text-dark d-block d-lg-none" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbar-ex-5"><i class="ti ti-menu-2 ti-md"></i></a><div class="collapse navbar-collapse" id="navbar-ex-5"><ul class="navbar-nav ms-lg-auto"><li class="nav-item"><label class="btn btn-label-primary"><i class="tf-icons navbar-icon ti ti-cloud-upload ti-xs me-1" style="margin-top: -2px;"></i> Loading...</label></li></ul></div></div></nav><div class="card-body bg-lighter d-flex justify-content-center" id="app-ilsya-files-content" style="align-items: center;height: calc(100vh - 12rem) !important; overflow: auto;"><div class="sk-grid sk-secondary"><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div></div></div></div>');
            },
            success: (res) => {
                this.body.html(res);
            },
            error: (err) => { console.log(err); }
        })
    }

    trimUrl(url) {
        return url.replace(/([^:]\/)\/+/g, "$1")
    }


}
