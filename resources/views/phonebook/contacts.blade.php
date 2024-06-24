@extends('dash.layouts.app')

@section('title', 'CONTACTS')

@section('content')

    <nav class="navbar navbar-expand-lg bg-navbar-theme rounded">
        <div class="container-fluid">
            <a class="navbar-brand" href="javascript:void(0)">Contacts</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-ex-5">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar-ex-5">
                <div class="navbar-nav me-auto">
                </div>
                <ul class="navbar-nav ms-lg-auto">
                    <li class="nav-item">
                        <button class="btn btn-label-danger me-3 mb-xl-0 mb-lg-0 mb-2 is-phonebook-delete"
                            id="is-label-delete">
                            <i class="ti ti-trash me-1" style="margin-top: -2px"></i>
                            Delete PhoneBook
                        </button>
                    </li>
                    <li class="nav-item">

                        <a href="{!! route('phonebook') !!}" class="btn btn-label-dark">
                            <i class="ti ti-chevron-left me-1" style="margin-top: -2px"></i>
                            Back
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="mt-4">
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Name</th>
                            <th>Number</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-contacts-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">New Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-contacts-store" action="{!! route('phonebook.contacts.store', $label->id) !!}" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Name"
                                    autocomplete="off" />
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Number</label>
                                <input type="text" name="phone_number" class="form-control"
                                    placeholder="Enter Number ex: 6285xxxxxxx" required />
                            </div>
                            <small class="text-muted text-center  mb-3">You can enter whatsapp number or whatsapp group id
                                as long
                                as your bot has joined the group. or you can use sync groups to retrieve the groups you
                                entered.</small>

                            <div class="col-6 mb-3">
                                <label class="form-label">Birth Date</label>
                                <input type="text" name="birth_date" class="form-control" placeholder="YYYY-MM-DD"
                                    autocomplete="off" onfocus="(this.type='date')" onblur="(this.type='text')"
                                    pattern="[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])" />
                            </div>

                            <div class="col-6 mb-3">
                                <label class="form-label">Marital Status</label>
                                <select name="marital_status" class="form-select">
                                    <option value="">-- Select One --</option>
                                    <option value="1">Maried</option>
                                    <option value="0">Single</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Religion</label>
                                <select name="religion" class="form-select">
                                    <option value="">-- Select One --</option>
                                    <option value="islam">Islam</option>
                                    <option value="kristen">Kristen</option>
                                    <option value="katolik">Katolik</option>
                                    <option value="hindu">Hindu</option>
                                    <option value="buddha">Buddha</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">-- Select One --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Region</label>
                                <select name="region" class="form-select">
                                    <option value="">-- Select One --</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Area</label>
                                <select name="area" class="form-select">
                                    <option value="">-- Select One --</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"> {{ $area->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">City</label>
                                <select name="city" class="form-select">
                                    <option value="">-- Select One --</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Shop</label>
                                <select name="shop" class="form-select">
                                    <option value="">-- Select One --</option>
                                    @foreach ($shops as $shop)
                                        <option value="{{ $shop->id }}"> {{ $shop->name }}</option>
                                    @endforeach
                                </select>
                            </div>
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

    <div class="modal fade" id="modal-contacts-import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Import Contact <small>xlsx</small></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-contacts-import" action="{!! route('phonebook.contacts.import', $label->id) !!}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">File xlsx</label>
                                <input type="file" name="file" class="form-control">
                            </div>
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
@endsection

@push('js')
    <script>
        (function() {
            'use strict';
            var ilsya = new velixs()

            var dbs = ilsya.datatables({
                url: "{{ route('phonebook.contacts.ajax', $label->id) }}",
                url_delete: "{{ route('phonebook.contacts.delete', $label->id) }}",
                header: `PhoneBook : {{ $label->title }}`,
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
                        data: 'phone_number'
                    },
                    {
                        data: 'type'
                    }
                ],
                btn: [{
                        text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">New Contact</span>',
                        className: 'is-button-add btn btn-primary me-2 ',
                        attr: {
                            'data-bs-toggle': 'modal',
                            'data-bs-target': '#modal-contacts-add'
                        }
                    },
                    {
                        extend: 'collection',
                        className: 'btn btn-label-primary dropdown-toggle me-2',
                        text: '<i class="ti ti-3d-cube-sphere me-sm-1"></i> <span class="d-none d-sm-inline-block">Tools</span>',
                        buttons: [{
                            text: '<i class="ti ti-file-import me-1"></i> Import Xlsx',
                            className: 'dropdown-item',
                            attr: {
                                'data-bs-toggle': 'modal',
                                'data-bs-target': '#modal-contacts-import'
                            }
                        }, {
                            text: '<i class="ti ti-file-export me-1"></i> Export Xlsx',
                            className: 'dropdown-item',
                            action: function() {
                                window.location.href =
                                    '{{ route('phonebook.contacts.export', $label->id) }}';
                            }
                        }, {
                            text: '<i class="ti ti-users me-1"></i> Fetch All Group',
                            className: 'dropdown-item is-fetch-all-group',
                        }, ]
                    },
                    {
                        text: '<i class="ti ti-trash me-sm-1"></i> <span class="d-none d-sm-inline-block">Delete Contacts</span>',
                        className: 'is-button-delete btn me-2 btn-label-danger'
                    }
                ],
            })

            $(document).on('click', '#is-label-delete', function() {
                Swal.fire({
                    text: 'If you delete a label, you will delete all contacts on that label.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        ilsya.ajax({
                            url: '{!! route('phonebook.delete', $label->id) !!}',
                            type: "GET",
                            data: {},
                            addons_success: function(get) {
                                get.swal.then(function() {
                                    window.location.href = "{!! route('phonebook') !!}"
                                })
                            }
                        })
                    }
                })
            })

            $("#form-contacts-import").submit(function(e) {
                e.preventDefault()
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: new FormData(this),
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        ilsya.swal_loading({
                            message: '<small class="is-loading-ilsya-persen">0</small>'
                        })
                    },
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        return ilsya.xhr_progess({
                            xhr,
                            target: '.is-loading-ilsya-persen'
                        })
                    },
                    success: function(data) {
                        $("#modal-contacts-import").modal('hide')
                        dbs.ajax.reload()
                        $("#form-contacts-import")[0].reset()
                        ilsya.swal_success({
                            message: data.message
                        })
                    },
                    error: function(data) {
                        $("#modal-contacts-import").modal('hide')
                        ilsya.swal_error({
                            message: data.responseJSON.message
                        })
                    }
                });
            })

            $(document).on('click', '.is-fetch-all-group', function(e) {
                e.preventDefault()
                $.ajax({
                    url: '{!! route('phonebook.contacts.fetchgroup', $label->id) !!}',
                    type: "GET",
                    cache: false,
                    data: {},
                    beforeSend: function() {
                        ilsya.swal_loading({})
                    },
                    success: function(data) {
                        dbs.ajax.reload()
                        ilsya.swal_success({
                            message: data.message
                        })
                    },
                    error: function(data) {
                        ilsya.swal_error({
                            message: data.responseJSON.message
                        })
                    }
                })
            })

            $("#form-contacts-store").submit(function(e) {
                e.preventDefault()
                var form = $(this)
                ilsya.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: form.serialize(),
                    addons_success: function() {
                        $("#modal-contacts-add").modal('hide')
                        form[0].reset()
                        dbs.ajax.reload()
                    }
                })
            })

        })()
    </script>
@endpush

@push('cssvendor')
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{!! asset('assets') !!}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{!! asset('assets') !!}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
@endpush

@push('jsvendor')
    <script src="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush
