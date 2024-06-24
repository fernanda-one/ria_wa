@extends('dash.layouts.app')

@section('title', 'DEVICE')

@section('content')
    <div class="row">
        <div class="col-12 col-xl-8 col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-center align-items-center" style="height: 25rem" id="digidaw-velixs">
                        @if ($device->status == 'CONNECTED')
                            <div class="d-block">
                                <div class="d-flex justify-content-center">
                                    <div class="sk-fold sk-secondary">
                                        <div class="sk-fold-cube"></div>
                                        <div class="sk-fold-cube"></div>
                                        <div class="sk-fold-cube"></div>
                                        <div class="sk-fold-cube"></div>
                                    </div>
                                </div>
                                <div class="d-block" style="padding-top:40px">
                                    <div class="text-muted">WAITING FOR SERVER RESPONSE</div>
                                </div>
                            </div>
                        @else
                            <div class="d-block">
                                <div class="d-flex justify-content-center">
                                    <div class="sk-fold sk-secondary">
                                        <div class="sk-fold-cube"></div>
                                        <div class="sk-fold-cube"></div>
                                        <div class="sk-fold-cube"></div>
                                        <div class="sk-fold-cube"></div>
                                    </div>
                                </div>
                                <div class="d-block" style="padding-top:40px">
                                    <div class="text-muted" id="status-waiting">CLICK START SESSION !</div>
                                </div>
                            </div>
                            <div class="d-block">
                                <div class="text-center" style="position: absolute; right: 0; bottom: 30px; left: 0;">
                                    <button class="btn btn-primary startbutton">START SESSION</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Device Name :</span>
                                <span>{{ $device->session_name }}</span>
                            </li>
                            <span id="content-detail">
                                <li class="mb-2">
                                    <span class="fw-semibold me-1">Session Name :</span>
                                    <span>-</span>
                                </li>
                                <li class="mb-2">
                                    <span class="fw-semibold me-1">Whatsapp Number :</span>
                                    <span>-</span>
                                </li>
                            </span>
                        </ul>
                        <div class="d-flex justify-content-center mt-5">
                            <button class="btn w-50 is-logout btn-label-danger suspend-user waves-effect">Log out</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Logs</h6>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped border-top">
                    <tbody id="logger">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('css')
    {{-- meta device --}}
    <meta name="device_id" content="{{ $device->id }}">
    <meta name="device_status" content="{{ $device->status }}">
@endpush

@push('js')
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
            attempts++;
            if (attempts >= limit_attempts) {
                socket.disconnect();
            }
        });

        socket.on('connect', () => {
            $("#server-status").html('<span class="badge rounded-pill bg-label-primary d-flex align-items-center "><i class="ti ti-server-2 me-1"></i><span style="padding-top: 2px"><span class="d-none d-xl-inline d-lg-inline d-md-inline">SERVER - </span>CONNECTED</span></span>')
            attempts = 0;
        });
    </script>
    <script src="{!! asset('assets/libvelixs/ilsya_client.js') !!}"></script>
    <script>
        var ilsya_client = new IlsyaClient(socket);

        ilsya_client.init();

        $(document).on('click', ".startbutton", function(e) {
            e.preventDefault()
            $(this).attr('disabled', true)
            $("#status-waiting").html('WAITING FOR SERVER RESPONSE');
            $(this).html('<span class="spinner-grow me-1" role="status" aria-hidden="true"></span>LOADING... ')
            ilsya_client.startSession()
        });

        $(document).on('click', ".refresh-page", function(e) {
            e.preventDefault()
            location.reload();
        });

        $(document).on('click', ".is-logout", function(e) {
            e.preventDefault()
            $(this).attr('disabled', true)
            $("#status-waiting").html('WAITING FOR SERVER RESPONSE');
            $(this).html('<span class="spinner-grow me-1" role="status" aria-hidden="true"></span>Loading... ')
            ilsya_client.logout()

            setTimeout(function() {
                $(".is-logout").attr('disabled', false)
                $(".is-logout").html('Log out')
            }, 8000);
        });
    </script>
@endpush
