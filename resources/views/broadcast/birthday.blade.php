@extends('dash.layouts.app')

@section('title', 'Birthday')


@section('content')
    <div class="card">
        <div class="card-body">
            <div class="alert alert-primary d-flex align-items-center" role="alert">
                <span class="alert-icon text-primary me-2">
                    <i class="ti ti-device-mobile ti-xs"></i>
                </span>
                <div class="d-block">
                    You send messages using<span class="fw-bold"> {{ $main_device->session_name }} {!! $main_device->whatsapp_number ? "<small>($main_device->whatsapp_number)</small>" : '' !!}
                    </span> device.
                </div>
            </div>
            <form id="form-store-single" action="{!! route('broadcast.birthday.store') !!}" enctype="multipart/form-data" method="POST">
                @csrf
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
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFullTitle">Add New Responder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="responder-store" action="{{ route('responder.store') }}" method="post" style="display: contents"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <span class="alert-icon text-primary me-2">
                                <i class="ti ti-device-mobile ti-xs"></i>
                            </span>
                            <div class="d-block">
                                You create an autoresponder for <span class="fw-bold"> {{ $main_device->session_name }}
                                    {!! $main_device->whatsapp_number ? "<small>($main_device->whatsapp_number)</small>" : '' !!} </span> device.
                            </div>
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
    </script>
@endpush
