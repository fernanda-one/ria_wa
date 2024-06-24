@extends('dash.layouts.app')

@section('title', 'RESPONDERS')


@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Keyword</th>
                        <th>type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFullTitle">Add New Responder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="responder-store" action="{{ route('responder.store') }}" method="post" style="display: contents" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <span class="alert-icon text-primary me-2">
                                <i class="ti ti-device-mobile ti-xs"></i>
                            </span>
                            <div class="d-block">
                                You create an autoresponder for <span class="fw-bold"> {{ $main_device->session_name }} {!! $main_device->whatsapp_number ? "<small>($main_device->whatsapp_number)</small>" : '' !!} </span> device.
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Keyword</label>
                                    <input type="text" name="keyword" class="form-control" autocomplete="off" required placeholder="ex: !help">
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Message Type</label>
                                    <select name="message_type" required class="form-select">
                                        <option value="">-- Select One --</option>
                                        <option value="text">Text Message</option>
                                        <option value="media">Media Message</option>
                                        {{-- <option value="button">Button Message</option>
                                        <option value="list">List Button Message</option> --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Keyword Type</label>
                                    <select name="type_keyword" required class="form-select">
                                        <option value="equal">Equal</option>
                                        <option value="contains">Contain</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Quoted</label>
                                    <select name="quoted" required class="form-select">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Reply Only When</label>
                                    <select name="reply_when" required class="form-select">
                                        <option value="all">All</option>
                                        <option value="personal">Personal</option>
                                        <option value="group">Group</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 col-lg-6"">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" required class="form-select">
                                        <option value="active">Active</option>
                                        <option value="inactive">InActive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="message-content">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-files" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-0 py-1">

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{!! asset('assets/libvelixs/ilsya.files.js') !!}"></script>
    <script src="{!! asset('assets/libvelixs/ilsya.message.js?v=233') !!}"></script>
    <script>
        var ilsya = new velixs()
        var files = new FileManager({
            subfolder: "{{ $auth->id }}",
            base_url: "{{ route('ilsya.files.index') }}"
        });

        var dbs = ilsya.datatables({
            url: "{{ route('responder') }}",
            url_delete: "{{ route('responder.delete') }}",
            header: `AutoResponder ( {{ $main_device->session_name }} )`,
            columns: [{
                    data: 'responsive_id'
                },
                {
                    data: 'responsive_id'
                },
                {
                    data: 'keyword'
                },
                {
                    data: 'message_type'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action'
                }
            ],
            btn: [{
                text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New</span>',
                className: 'is-button-add btn btn-primary me-2 '
            }, {
                text: '<i class="ti ti-trash me-sm-1"></i> <span class="d-none d-sm-inline-block">Delete</span>',
                className: 'is-button-delete btn me-2 btn-label-danger'
            }],
        })

        $("#responder-store").submit(function(e) {
            e.preventDefault()
            ilsya.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                addons_success: function() {
                    dbs.ajax.reload()
                    $("#responder-store")[0].reset()
                    $("#modal-add").modal('hide')
                    $("#message-content").html("")
                }
            })
        })

        $(".is-button-add").on("click", function(e) {
            $("#modal-add").modal("show")
        });

        $(document).on('click', '.is-change-status', function() {
            let thiss = $(this);
            ilsya.ajax({
                url: '{{ route('responder.status') }}',
                data: {
                    id: thiss.data('id'),
                },
                success: function(res) {
                    Swal.close();
                    if (res.status == 'active') {
                        thiss.removeClass('bg-label-dark').addClass('bg-label-primary').html('Active')
                    } else {
                        thiss.removeClass('bg-label-primary').addClass('bg-label-dark').html('InActive')
                    }
                }
            })
        });
    </script>
@endpush


@push('cssvendor')
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <style>
        /* zoom animation hover */
        .is-change-status {
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .is-change-status:hover {
            transform: scale(1.1);
        }
    </style>
@endpush

@push('jsvendor')
    <script src="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush
