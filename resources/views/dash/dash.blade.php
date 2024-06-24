@extends('dash.layouts.app')

@section('title', 'DASHBOARD')


@section('content')
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Devices</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2" id="count-device">{{ $count_device }}</h4>
                                <span class="text-success">({{ $count_device_online }} Online)</span>
                            </div>
                            <span>Limit Device : {{ $auth->limit_device ? $auth->limit_device : 'Unlimited' }}</span>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="ti ti-device-mobile ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Device</th>
                        <th>number</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="add-new" aria-labelledby="offcanvasEndLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasEndLabel" class="offcanvas-title">Add New Device</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <form id="device-store" action="{!! route('device.store') !!}" method="post">
                <div class="mb-3">
                    <label class="form-label" for="">Session Name</label>
                    <input class="form-control" type="text" name="session_name" required placeholder="Name Device">
                </div>

                <button type="submit" class="btn btn-primary mb-2 d-grid w-100">Submit</button>
                <button type="reset" class="btn btn-label-secondary d-grid w-100" data-bs-dismiss="offcanvas">
                    Cancel
                </button>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal-device-settings" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="device-settings-update" action="{!! route('device.settings.update') !!}" method="post">
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        (function() {
            var ilsya = new velixs()
            var dbs = ilsya.datatables({
                url: "{{ route('dashboard') }}",
                header: 'Whatsapp Devices',
                columns: [{
                        data: 'responsive_id'
                    },
                    {
                        data: 'session_name'
                    },
                    {
                        data: 'whatsapp_number'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action'
                    }
                ],
            })

            $(document).on('click', '.is-delete-device', function(e) {
                Swal.fire({
                    html: "You won't be able to revert this! <br>Make sure the device has loggedout.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        ilsya.ajax({
                            url: "{{ route('device.delete') }}",
                            data: {
                                id: $(this).data('id')
                            },
                            addons_success: function() {
                                dbs.ajax.reload()
                                main_device()
                                $("#count-device").html(parseInt($("#count-device").html()) - 1)
                            }
                        })
                    }
                })
            })

            // offcanvas show add new
            $(document).on('click', '.is-button-add', function(e) {
                $('#add-new').offcanvas('show')
            })

            $(document).on('click', '.generate-apikey', function(e) {
                ilsya.ajax({
                    url: "{{ route('ajax.generate_key') }}",
                    type: 'get',
                    success: function(res) {
                        Swal.close()
                        $("input[name='api_key']").val(res.apikey)
                    }
                })
            })

            $("#device-store").submit(function(e) {
                e.preventDefault()
                ilsya.ajax({
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    addons_success: function() {
                        dbs.ajax.reload()
                        $('#add-new').offcanvas('hide')
                        main_device()
                        $("#device-store")[0].reset()
                        $("#count-device").html(parseInt($("#count-device").html()) + 1)
                    }
                })
            })

            $("#device-settings-update").submit(function(e) {
                e.preventDefault()
                ilsya.ajax({
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    addons_success: function() {
                        $("#modal-device-settings").modal('hide')
                        $("#device-settings-update")[0].reset()
                    }
                })
            })

            $(document).on('click', '.is-show-settings', function(e) {
                var id = $(this).data('id')
                ilsya.ajax({
                    url: "{{ route('ajax.device.settings') }}",
                    data: {
                        id: id
                    },
                    beforeSend: function() {
                        $("#modal-device-settings").modal("show");
                        $("#modal-device-settings .modal-body").html(`<div class="d-flex justify-content-center"><div class="sk-grid sk-secondary"><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div></div></div><br><div class="text-center">Please wait a moment...</div>`)
                        $("#modal-device-settings .modal-footer button[type=submit]").attr("disabled", true)
                    },
                    success: function(res) {
                        $("#modal-device-settings .modal-footer button[type=submit]").attr("disabled", false)
                        $("#modal-device-settings .modal-body").html(`<input type="hidden" value="${res.data.id}" name="id" required><div class="mb-3"><label class="form-label">Webhook Url</label><input type="text" name="webhook" class="form-control" value="${res.data.webhook}" placeholder="Enter Url"></div><div class="mb-3"><label class="form-label">Api Key</label><div class="input-group"><input type="text" name="api_key" class="form-control" value="${res.data.api_key}" placeholder="Enter Api Key" required><button class="btn btn-primary waves-effect generate-apikey" type="button">Generate</button></div></div>`)
                    },
                    addons_error() {
                        $("#modal-device-settings").modal("hide");
                    }
                })
            });
        })()
    </script>
@endpush

@push('cssvendor')
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
@endpush

@push('jsvendor')
    <script src="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{!! asset('assets/libvelixs/client-dist/socket.io.js') !!}"></script>
    <script>
        let limit_attempts = {{ config('app.attemp_socket') }};
        let attempts = 0;
        @if (config('app.socket_default'))
            const socket = io();
        @else
            const socket = io("{{ config('app.base_node') }}", {
                transports: ['websocket']
            });
        @endif

        socket.on('connect_error', () => {
            $("#server-status").html('<span class="badge rounded-pill bg-label-secondary d-flex align-items-center "><i class="ti ti-server-off me-1"></i><span style="padding-top: 2px"><span class="d-none d-xl-inline d-lg-inline d-md-inline">SERVER - </span>DISCONNECTED</span></span>')
            $(".status-connection").html(`<span class="badge rounded-pill bg-label-secondary"><span style="font-size: 1.05rem;" class="ti ti-plug-connected-x"></span> -</span>`)
            // limit attempts to reconnect
            attempts++;
            if (attempts >= limit_attempts) {
                socket.disconnect();
            }
        });

        socket.on('connect', () => {
            $("#server-status").html('<span class="badge rounded-pill bg-label-primary d-flex align-items-center "><i class="ti ti-server-2 me-1"></i><span style="padding-top: 2px"><span class="d-none d-xl-inline d-lg-inline d-md-inline">SERVER - </span>CONNECTED</span></span>')
            // datatables ajax reload
            $(".datatables-basic").DataTable().ajax.reload();
            attempts = 0;
        });
    </script>
@endpush
