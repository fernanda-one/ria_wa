@extends('dash.layouts.app')

@section('title', 'Birthday')


@section('content')
    <div class="mt-4">
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>campaign</th>
                            <th>Schedule</th>
                            <th>Delay</th>
                            <th>of</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">New Broadcast</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-store-single" action="{!! route('broadcast.birthday.store') !!}" enctype="multipart/form-data" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Birth Date</label>
                                <input type="text" name="birth_date" class="form-control" placeholder="YYYY-MM-DD"
                                    autocomplete="off" onfocus="(this.type='date')" onblur="(this.type='text')"
                                    pattern="[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])" />
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Schedule Date</label>
                                <input type="text" name="schedule_date" class="form-control" placeholder="YYYY-MM-DD"
                                    autocomplete="off" onfocus="(this.type='date')" onblur="(this.type='text')"
                                    pattern="[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])" />
                            </div>
                            <div class="mb-3 col-12 ">
                                <label class="form-label">Message Type</label>
                                <select name="message_type" required class="form-select">
                                    <option value="">-- Select One --</option>
                                    <option value="text">Text Message</option>
                                    {{-- <option value="media">Media Message</option> --}}
                                </select>
                            </div>

                            <div id="message-content">

                            </div>

                        </div>


                        <div class="modal-footer">
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">
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
    <script src="{!! asset('assets/libvelixs/ilsya.message.js?v=35') !!}"></script>
    <script>
        var ilsya = new velixs()
        var files = new FileManager({
            subfolder: "{{ $auth->id }}",
            base_url: "{{ route('ilsya.files.index') }}"
        });

        $("#form-store-single").submit(function(e) {
            e.preventDefault()
            ilsya.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                addons_success: function() {
                    $("#form-store-single")[0].reset()
                    $("#message-content").html("")
                }
            })
        })

        var dbs = ilsya.datatables({
            url: "{{ route('campaigns.ajax') }}",
            url_delete: "{{ route('campaigns.delete') }}",
            header: `<div class="d-flex" style="justify-items: center" href="javascript:void(0)"><i class="ti ti-brand-whatsapp ti-sm me-2"></i>Campaigns</div>`,
            columns: [{
                    data: 'responsive_id'
                },
                {
                    data: 'responsive_id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'scheduled_at'
                },
                {
                    data: 'delay'
                },
                {
                    data: 'of'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action'
                }
            ],
            btn: [{
                text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">New Broadcast</span>',
                className: 'is-button-add btn btn-primary me-2 ',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modal-add'
                }
            }, {
                text: '<i class="ti ti-trash me-sm-1"></i> <span class="d-none d-sm-inline-block">Delete</span>',
                className: 'is-button-delete btn me-2 btn-label-danger'
            }],
        })

        $(".is-button-add").on("click", function(e) {
            $("#modal-add").modal("show")
        });
    </script>
@endpush


@push('cssvendor')
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{!! asset('assets') !!}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
@endpush

@push('jsvendor')
    <script src="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush
