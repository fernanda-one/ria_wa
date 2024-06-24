@extends('dash.layouts.app')

@section('title', 'Campaign Detail')


@section('content')
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-info" aria-controls="info" aria-selected="true">
                    <i class="tf-icons ti ti-brand-whatsapp ti-xs me-1"></i> Info
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link is-button-table-reload" role="tab" data-bs-toggle="tab" data-bs-target="#tab-bulk" aria-controls="bulk" aria-selected="false">
                    <i class="tf-icons ti ti-message ti-xs me-1"></i> Bulks
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-info" role="tabpanel">

                <div class="alert alert-dark alert-dismissible d-flex align-items-baseline" role="alert">
                    <span class="alert-icon alert-icon-lg text-dark me-2">
                        <i class="ti ti-message ti-sm"></i>
                    </span>
                    <div class="d-flex flex-column ps-1">
                        <h5 class="alert-heading mb-2">Campaign Detail</h5>
                        <p class="mb-">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Name </td>
                                    <td>: {{ $row->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Phonebook</td>
                                    @isset($row->contact_label)
                                        <td>: <a href="{!! route('phonebook.contacts.index', $row->phonebook_id) !!}">{{ $row->contact_label->title }}</a></td>
                                    @else
                                        <td>: Unknown</td>
                                    @endisset
                                </tr>
                                <tr>
                                    <td class="fw-bold">Receiver </td>
                                    <td>: {{ \App\Helpers\Lyn::thousandsCurrencyFormat($row->bulk()->count()) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Schedule </td>
                                    <td>: {{ \Carbon\Carbon::parse($row->scheduled_at)->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status </td>
                                    <td>: {{ \Str::ucfirst($row->status) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Delay </td>
                                    <td>: {{ \Str::ucfirst($row->delay) }}/s</td>
                                </tr>
                            </tbody>
                        </table>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-6 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Message Type</label>
                            <select class="form-select" disabled>
                                @if ($row->message_type == 'text')
                                    <option selected>Text Message</option>
                                @elseif ($row->message_type == 'media')
                                    <option selected>Media Message</option>
                                @elseif ($row->message_type == 'button')
                                    <option selected>Button Message</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Summary</label>
                            <span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="11" style="width: 583px;">
                                <span class="selection">
                                    <span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false">
                                        <ul class="select2-selection__rendered">
                                            <li class="select2-selection__choice bg-label-warning" style="padding-right: 10px;">
                                                {{ \App\Helpers\Lyn::thousandsCurrencyFormat($row->bulk()->where('status', 'pending')->count()) }} Pending
                                            </li>
                                            <li class="select2-selection__choice bg-label-success" style="padding-right: 10px;">
                                                {{ \App\Helpers\Lyn::thousandsCurrencyFormat($row->bulk()->where('status', 'sent')->count()) }} Sent
                                            </li>
                                            <li class="select2-selection__choice bg-label-danger" style="padding-right: 10px;">
                                                {{ \App\Helpers\Lyn::thousandsCurrencyFormat($row->bulk()->where('status', 'failed')->count()) }} Failed
                                            </li>
                                            <li class="select2-selection__choice bg-label-secondary" style="padding-right: 10px;">
                                                {{ \App\Helpers\Lyn::thousandsCurrencyFormat($row->bulk()->where('status', 'invalid')->count()) }} Invalid
                                            </li>
                                        </ul>
                                    </span>
                                </span>
                            </span>
                        </div>
                    </div>
                    @if ($row->message_type == 'text')
                        <div class="col-12">
                            <div class="mb-3"><label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" required disabled>{!! $data->message !!}</textarea>
                            </div>
                        </div>
                    @elseif ($row->message_type == 'media')
                        <div class="col-12 col-xl-6 col-lg-6">
                            <div class="mb-3"><label class="form-label">Media</label>
                                <div class="input-group">
                                    <input disabled type="text" class="form-control" value="{{ $data->url }}" name="media" required>
                                    <button class="btn btn-primary waves-effect is-button-preview" type="button">Preview</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6 col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Media Mime</label><select disabled name="media_type" required class="form-select">
                                    <option {{ $data->media_type == 'image' ? 'selected' : '' }} value="image">Image</option>
                                    <option {{ $data->media_type == 'video' ? 'selected' : '' }} value="video">Video</option>
                                    <option {{ $data->media_type == 'audio' ? 'selected' : '' }} value="audio">Audio</option>
                                    <option {{ $data->media_type == 'file' ? 'selected' : '' }} value="file">File</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3"><label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" disabled>{!! $data->caption !!}</textarea>
                            </div>
                        </div>
                    @elseif ($row->message_type == 'button')
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" disabled required>{{ $data->message }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Footer</label>
                                <input name="footer" class="form-control" disabled value="{{ $data->footer }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table_button_message">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Display Text</th>
                                                <th>Respond</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0 is-content">
                                            @foreach ($data->buttons as $btn)
                                                <tr>
                                                    <td><input type="text" disabled class="form-control" required value="{{ $btn->display ?? '' }}"></td>
                                                    <td><input type="text" disabled class="form-control" required value="{{ $btn->id ?? '' }}"></td>
                                                    <td><button class="btn btn-label-danger" disabled type="button"><i class="ti ti-trash-x"></i></button></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif ($row->message_type == 'list')
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input name="title" class="form-control" value="{{ $data->title }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" required>{{ $data->message }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Footer</label>
                                <input name="footer" class="form-control" value="{{ $data->footer }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3"><label class="form-label">Button Text</label>
                                <input name="button_text" class="form-control" value="{{ $data->buttonText }}" required placeholder="Click Here" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table_button_message table-list-btn">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Display Text</th>
                                                <th>Respond</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0 is-content">
                                            @foreach ($data->sections as $btn)
                                                @isset($btn->title)
                                                    <tr>
                                                        <td>
                                                            <select disabled class="form-select" required="">
                                                                <option value="option">Option</option>
                                                                <option selected value="section">Section</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" disabled class="form-control" value="{{ $btn->title ?? '' }}" placeholder="Ex: Menu Click Me" required=""></td>
                                                        <td data-input-btn-id>
                                                            <input style="display: none" type="text" disabled value="display" placeholder="Ex: !menu" class="form-control" required="">
                                                            <div>-</div>
                                                        </td>
                                                        <td><button class="btn btn-label-danger" disabled type="button"><i class="ti ti-trash-x"></i></button></td>
                                                    </tr>
                                                @endisset
                                                @foreach ($btn->rows as $resbtn)
                                                    <tr>
                                                        <td>
                                                            <select disabled class="form-select" required="">
                                                                <option selected value="option">Option</option>
                                                                <option value="section">Section</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control" disabled value="{{ $resbtn->title ?? '' }}" placeholder="Ex: Menu Click Me" required=""></td>
                                                        <td data-input-btn-id><input type="text" disabled value="{{ $resbtn->rowId ?? '' }}" placeholder="Ex: !menu" class="form-control" required=""></td>
                                                        <td><button disabled class="btn btn-label-danger" type="button"><i class="ti ti-trash-x"></i></button></td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
            <div class="tab-pane fade " id="tab-bulk" role="tabpanel">
                <div class=" table-responsive pt-0">
                    <table class="datatables-basic table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Receiver</th>
                                <th>type</th>
                                <th>Status</th>
                                <th>Updated_at</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-preview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        'use strict';
        var ilsya = new velixs()

        var dbs = ilsya.datatables({
            url: "{{ route('campaigns.detail.ajax', $row->id) }}",
            header: `List Receiver`,
            columns: [{
                    data: 'responsive_id'
                },
                {
                    data: 'receiver'
                },
                {
                    data: 'type'
                },
                {
                    data: 'status'
                },
                {
                    data: 'updated_at'
                },
            ],
            btn: [],
        })

        $(".is-button-preview").on("click", function() {
            var url = $("input[name='media']").val()
            var message_type = $("select[name='media_type']").val()
            $("#modal-preview").modal("show")
            switch (message_type) {
                case 'image':
                    $("#modal-preview .modal-body").html(`<img src="${url}" class="img-fluid" alt="">`)
                    break
                case 'video':
                    $("#modal-preview .modal-body").html(`<div class="text-center"><video style="width: 100%;" controls><source  src="${url}" type="video/mp4">Your browser does not support the video tag.</video></div>`)
                    break
                case 'audio':
                    $("#modal-preview .modal-body").html(`<div class="text-center"><audio controls><source src="${url}" type="audio/mpeg">Your browser does not support the audio element.</audio></div>`)
                    break
                case 'file':
                    $("#modal-preview .modal-body").html(`<div class="text-center"><a href="${url}" class="btn btn-primary">Download</a></div>`)
                    break
            }
        })

        $("#modal-preview").on("hidden.bs.modal", function() {
            $("#modal-preview .modal-body").html("")
        })

        $(".is-button-table-reload").on("click", function() {
            dbs.ajax.reload()
        })
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
@endpush
