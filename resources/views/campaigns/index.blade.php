@extends('dash.layouts.app')

@section('title', 'CAMPAIGNS')

@section('content')
    <div class="mt-4">
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
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
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFullTitle">New Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="bs-stepper wizard-numbered mt-2">
                        <div class="bs-stepper-header">
                            <div class="step" data-target="#step1">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">1</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Step 1</span>
                                        <span class="bs-stepper-subtitle">Enter Name Campaign</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <div class="step" data-target="#step2">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">2</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Step 2</span>
                                        <span class="bs-stepper-subtitle">Make Message & Select a recipient.</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <div class="step" data-target="#step3">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">3</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Step 3</span>
                                        <span class="bs-stepper-subtitle">Delays and Scheduling.</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="bs-stepper-content">
                            <form id="form-campaign-store" method="POST" onSubmit="return false">
                                <div id="step1" class="content">
                                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                                        <span class="alert-icon text-primary me-2">
                                            <i class="ti ti-device-mobile ti-xs"></i>
                                        </span>
                                        <div class="d-block">
                                            Create Campaign for <span class="fw-bold"> {{ $main_device->session_name }}
                                                {!! $main_device->whatsapp_number ? "<small>($main_device->whatsapp_number)</small>" : '' !!} </span> device.
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12 col-xl-6 col-lg-6">
                                            <label class="form-label" for="username">Campaign Name</label>
                                            <input type="text" name="name" class="form-control"
                                                placeholder="Enter Name" required />
                                        </div>
                                        <div class="col-12 col-xl-6 col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Phonebook <small
                                                        class="text-muted">receiver</small></label>
                                                <select name="phonebook_id" required class="form-select">
                                                    <option value="">-- Select One --</option>
                                                    @foreach ($phonebook as $pb)
                                                        <option value="{{ $pb->id }}">{{ $pb->title }}
                                                            ({{ $pb->contacts_count }} Contacts)</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between">
                                            <button class="btn btn-label-secondary btn-prev" disabled type="button">
                                                <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </button>
                                            <button class="btn btn-primary btn-next" type="button" data-id="step1">
                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                <i class="ti ti-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Personal Info -->
                                <div id="step2" class="content">
                                    <div class="row">
                                        <div class="col-12">
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
                                    </div>
                                    <div id="message-content">

                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev" disabled type="button">
                                            <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next" type="submit" data-id="step2">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                            <i class="ti ti-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="step3" class="content">
                                    <div class="row g-3">
                                        <div class="col-12 col-xl-6 col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Delay / Message</label>
                                                <input type="number" name="delay" class="form-control" required
                                                    min="0" value="10">
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-6 col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Scheduled at</label>
                                                <input type="datetime-local" name="scheduled_at" class="form-control"
                                                    required value="{{ now() }}">
                                                <small>The default time indicates the message will be sent
                                                    immediately.</small>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between">
                                            <button class="btn btn-label-secondary btn-prev" disabled type="button">
                                                <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-next is-campaign-store"
                                                data-id="step2">
                                                <span class="align-middle">Create Campaign</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
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
    <script src="{!! asset('assets/libvelixs/ilsya.message.js?v=12') !!}"></script>
    <script>
        var ilsya = new velixs()
        var files = new FileManager({
            subfolder: "{{ $auth->id }}",
            base_url: "{{ route('ilsya.files.index') }}"
        });

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
                text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">New Campaign</span>',
                className: 'is-button-add btn btn-primary me-2 '
            }, {
                text: '<i class="ti ti-trash me-sm-1"></i> <span class="d-none d-sm-inline-block">Delete</span>',
                className: 'is-button-delete btn me-2 btn-label-danger'
            }],
        })

        $(document).on('click', '.is-change-status', function() {
            var id = $(this).data('id')
            var status = $(this).data('status')
            $.ajax({
                url: "{{ route('campaigns.ajax.changestatus') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                    status: status
                },
                beforeSend: function() {
                    ilsya.swal_loading({})
                },
                success: function(data) {
                    ilsya.swal_success({
                        message: data.message
                    })
                    dbs.ajax.reload()
                },
                error: function(data) {
                    ilsya.swal_error({
                        message: data.responseJSON.message
                    })
                }
            })
        })

        $(".is-button-add").on("click", function(e) {
            $("#modal-add").modal("show")
        });

        const wizardNumbered = document.querySelector('.wizard-numbered'),
            wizardNumberedBtnNextList = [].slice.call(wizardNumbered.querySelectorAll('.btn-next')),
            wizardNumberedBtnPrevList = [].slice.call(wizardNumbered.querySelectorAll('.btn-prev'));

        const numberedStepper = new Stepper(wizardNumbered, {
            linear: false
        });
        if (wizardNumberedBtnNextList) {
            wizardNumberedBtnNextList.forEach(wizardNumberedBtnNext => {
                wizardNumberedBtnNext.addEventListener('click', event => {
                    switch ($(wizardNumberedBtnNext).data('id')) {
                        case "step1":
                            if ($("input[name='name']").val() == "") {
                                ilsya.swal_error({
                                    message: "Name is required"
                                })
                                return false
                            }
                            if ($("select[name='phonebook_id']").val() == "") {
                                ilsya.swal_error({
                                    message: "Phonebook is required"
                                })
                                return false
                            }
                            break
                        case 'step2':
                            var invalidInputs = document.querySelectorAll('#step2 :invalid');
                            if (invalidInputs.length > 0) {
                                invalidInputs[0].focus();
                                return false
                            }
                            break
                    }
                    numberedStepper.next();
                });
            });
        }
        if (wizardNumberedBtnPrevList) {
            wizardNumberedBtnPrevList.forEach(wizardNumberedBtnPrev => {
                wizardNumberedBtnPrev.addEventListener('click', event => {
                    numberedStepper.previous();
                });
            });
        }

        $(".is-campaign-store").on('click', function() {
            var form = $("#form-campaign-store")
            form.submit()
            var invalidInputs = document.querySelectorAll('#form-campaign-store :invalid');
            if (invalidInputs.length > 0) {
                invalidInputs[0].focus();
                return false
            }
            var formData = new FormData(form[0])
            $.ajax({
                url: "{{ route('campaigns.store') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    ilsya.swal_loading({})
                },
                success: function(data) {
                    ilsya.swal_success({
                        message: data.message,
                        timer: 5000
                    })
                    dbs.ajax.reload()
                    $("#message-content").val("")
                    $("#modal-add").modal("hide")
                    form[0].reset()
                    numberedStepper.to(0)
                },
                error: function(data) {
                    ilsya.swal_error({
                        message: data.responseJSON.message
                    })
                }
            })
        })
    </script>
@endpush


@push('cssvendor')
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{!! asset('assets') !!}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{!! asset('assets') !!}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />

    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/bs-stepper/bs-stepper.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/select2/select2.css" />
@endpush

@push('jsvendor')
    <script src="{!! asset('assets') !!}/vendor/libs/bs-stepper/bs-stepper.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/select2/select2.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush
