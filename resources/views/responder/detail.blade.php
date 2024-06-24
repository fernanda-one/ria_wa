@extends('dash.layouts.app')

@section('title', 'RESPONDERS')


@section('content')
    <div class="card">
        <div class="card-body">
            <form id="form-update-responder" action="{!! route('responder.update', $row->id) !!}" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12 col-xl-6 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Keyword</label>
                            <input type="text" name="keyword" class="form-control" autocomplete="off" value="{{ $row->keyword }}" required placeholder="ex: !help">
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Message Type</label>
                            <select class="form-select" disabled>
                                @if ($row->message_type == 'text')
                                    <option selected>Text Message</option>
                                @else ($row->message_type == 'media')
                                    <option selected>Media Message</option>
                                @endif 
                                {{-- ($row->message_type == 'button') --}}
                                    {{-- <option selected>Button Message</option>
                                @elseif ($row->message_type == 'list')
                                    <option selected>List Button Message</option>
                                @endif --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Keyword Type</label>
                            <select name="type_keyword" required class="form-select">
                                <option {{ $row->type_keyword == 'equal' ? 'selected' : '' }} value="equal">Equal</option>
                                <option {{ $row->type_keyword == 'contains' ? 'selected' : '' }} value="contains">Contain</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Quoted</label>
                            <select name="quoted" required class="form-select">
                                <option {{ $data->quoted ?? '' == 'no'? 'selected' : '' }} value="no">No</option>
                                <option {{ $data->quoted ?? '' == 'yes'? 'selected' : '' }} value="yes">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Reply Only When</label>
                            <select name="reply_when" required class="form-select">
                                <option {{ $row->reply_when == 'all' ? 'selected' : '' }} value="all">All</option>
                                <option {{ $row->reply_when == 'personal' ? 'selected' : '' }} value="personal">Personal</option>
                                <option {{ $row->reply_when == 'group' ? 'selected' : '' }} value="group">Group</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 col-lg-6"">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" required class="form-select">
                                <option {{ $row->status == 'active' ? 'selected' : '' }} value="active">Active</option>
                                <option {{ $row->status == 'inactive' ? 'selected' : '' }} value="inactive">InActive</option>
                            </select>
                        </div>
                    </div>


                    @if ($row->message_type == 'text')
                        <div class="col-12">
                            <div class="mb-3"><label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" required>{!! $data->message !!}</textarea>
                            </div>
                        </div>
                    @elseif ($row->message_type == 'media')
                        <div class="col-12 col-xl-6 col-lg-6">
                            <div class="mb-3"><label class="form-label">Media</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $data->url }}" name="media" required>
                                    <button class="btn btn-primary waves-effect filemanagerilsya" type="button">Upload</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6 col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Media Mime</label><select name="media_type" required class="form-select">
                                    <option {{ $data->media_type == 'image' ? 'selected' : '' }} value="image">Image</option>
                                    <option {{ $data->media_type == 'video' ? 'selected' : '' }} value="video">Video</option>
                                    <option {{ $data->media_type == 'audio' ? 'selected' : '' }} value="audio">Audio</option>
                                    <option {{ $data->media_type == 'file' ? 'selected' : '' }} value="file">File</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3"><label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control">{!! $data->caption !!}</textarea>
                            </div>
                        </div>
                    @elseif ($row->message_type == 'button')
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
                            <div class="table_button_message table-btn">
                                <div class="text-end"><button type="button" class="btn btn-label-primary is-add">Add Button</button></div>
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
                                                    <td><input type="text" name="btn_display[]" class="form-control" required value="{{ $btn->display ?? '' }}"></td>
                                                    <td><input type="text" name="btn_id[]" class="form-control" required value="{{ $btn->id ?? '' }}"></td>
                                                    <td><button class="btn btn-label-danger is-delete" type="button"><i class="ti ti-trash-x"></i></button></td>
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
                                <div class="text-end"><button type="button" class="btn btn-label-primary is-add">Add Button</button></div>
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
                                                            <select name="type[]" class="form-select" required="">
                                                                <option value="option">Option</option>
                                                                <option selected value="section">Section</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="btn_display[]" class="form-control" value="{{ $btn->title }}" placeholder="Ex: Menu Click Me" required=""></td>
                                                        <td data-input-btn-id>
                                                            <input style="display: none" type="text" name="btn_id[]" value="display" placeholder="Ex: !menu" class="form-control" required="">
                                                            <div>-</div>
                                                        </td>
                                                        <td><button class="btn btn-label-danger is-delete" type="button"><i class="ti ti-trash-x"></i></button></td>
                                                    </tr>
                                                @endisset
                                                @foreach ($btn->rows as $row)
                                                    <tr>
                                                        <td>
                                                            <select name="type[]" class="form-select" required="">
                                                                <option selected value="option">Option</option>
                                                                <option value="section">Section</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="btn_display[]" class="form-control" value="{{ $row->title }}" placeholder="Ex: Menu Click Me" required=""></td>
                                                        <td data-input-btn-id><input type="text" name="btn_id[]" value="{{ $row->rowId }}" placeholder="Ex: !menu" class="form-control" required=""></td>
                                                        <td><button class="btn btn-label-danger is-delete" type="button"><i class="ti ti-trash-x"></i></button></td>
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
                <div class="text-end mt-4">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <a href="{!! route('responder') !!}" class="btn btn-secondary">Back Responders</a>
                </div>
            </form>
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
    <script src="{!! asset('assets/libvelixs/ilsya.files.js') !!}"></script>'
    <script src="{!! asset('assets/libvelixs/ilsya.message.js') !!}"></script>
    <script>
        (function() {
            'use strict';

            var ilsya = new velixs()
            var files = new FileManager({
                subfolder: "{{ $auth->id }}",
                base_url: "{{ route('ilsya.files.index') }}"
            });


            $("#form-update-responder").submit(function(e) {
                e.preventDefault()
                ilsya.ajax({
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    // addons_success: function() {
                    // }
                })
            })

            $(document).on('click', ".filemanagerilsya", function() {
                $("#modal-files").modal('show')
                files.init({
                    body: "#modal-files .modal-body",
                })
            });

            $(document).on('click', '.getfiles-ilsya', function() {
                var path = $(this).data('path');
                $("input[name='media']").val("{{ asset('storage') }}" + "/" + path)
                $("#modal-files").modal('hide')
            })
        })()
    </script>
@endpush
