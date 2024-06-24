@extends('dash.layouts.app')

@section('title', 'PHONEBOOK')

@section('content')

    <nav class="navbar navbar-expand-lg bg-navbar-theme rounded">
        <div class="container-fluid">
            <a class="navbar-brand" href="javascript:void(0)">Phonebook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-ex-5">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar-ex-5">
                <div class="navbar-nav me-auto">
                </div>
                <ul class="navbar-nav ms-lg-auto">
                    <li class="nav-item">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-label-add">Add PhoneBook</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="mt-4" id="is-label-content">
        @foreach ($phonebook->get() as $row)
            <a href="{{ route('phonebook.contacts.index', $row->id) }}" class="card mb-2 cursor-pointer card-zoom navbar-brand">
                <div class="card-body py-3 d-flex" style="align-items: center; justify-content: space-between !important">
                    <div>
                        {{-- <i class="ti ti-brand-angular ti-lg text-danger me-3"></i> --}}
                        <i class="ti ti-address-book ti-lg text-dark me-3"></i><span>{{ $row->title }}</span>
                    </div>
                    <small>{{ \App\Helpers\Lyn::thousandsCurrencyFormat($row->contacts()->count()) }} contacts</small>
                </div>
            </a>
        @endforeach
    </div>

    <div class="modal fade" id="modal-label-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add Label</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-label-store" action="{!! route('phonebook.ajax.label.store') !!}" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Label</label>
                                <input type="text" name="title" class="form-control" placeholder="Enter Label Name" />
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
            $("#form-label-store").submit(function(e) {
                e.preventDefault()
                var form = $(this)
                ilsya.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: form.serialize(),
                    addons_success: function(get) {
                        let res = get.res;
                        $("#modal-label-add").modal('hide')
                        form[0].reset()
                        $("#is-label-content").append(`<a href="${res.data.url}" class="card mb-2 cursor-pointer card-zoom navbar-brand"><div class="card-body py-3 d-flex" style="align-items: center; justify-content: space-between !important"><div>{{-- <i class="ti ti-brand-angular ti-lg text-danger me-3"></i> --}}<i class="ti ti-address-book ti-lg text-dark me-3"></i><span>${res.data.title}</span></div><small>0 contacts</small></div></a>`)
                    }
                })
            })
        })()
    </script>
@endpush

@push('css')
    <style>
        .card-zoom {
            transition: 0.3s;
        }

        .card-zoom:hover {
            transform: scale(1.01);
        }
    </style>
@endpush
