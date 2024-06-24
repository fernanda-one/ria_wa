@extends('dash.layouts.app')

@section('title', 'MANAGE USERS')


@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Limit</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFullTitle">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="user-store" action="{{ route('admin.users.store') }}" method="post" style="display: contents" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="name">Username</label>
                            <input type="text" class="form-control" name="username" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="name">Password</label>
                            <input type="text" class="form-control" name="password" required autocomplete="off">
                        </div>
                        <div class="row">
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="name">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="name">Limit Device</label>
                                    <input type="number" min="0" value="0" class="form-control" name="limit_device" required autocomplete="off">
                                    <small>if you want unlimited fill it with 0</small>
                                </div>
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

    <div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFullTitle">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="user-update" action="{{ route('admin.users.update') }}" method="post" style="display: contents" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="name">Username</label>
                            <input type="text" class="form-control" name="username" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="name">Password</label>
                            <input type="text" class="form-control" name="password" autocomplete="off">
                            <small>Ignore it if you don't want to change the password</small>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="name">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="name">Limit Device</label>
                                    <input type="number" min="0" value="0" class="form-control" name="limit_device" required autocomplete="off">
                                    <small>if you want unlimited fill it with 0</small>
                                </div>
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

@endsection

@push('js')
    <script>
        var ilsya = new velixs()
        var dbs = ilsya.datatables({
            url: "{{ route('admin.users.ajax') }}",
            header: `Manage Users`,
            columns: [{
                    data: 'responsive_id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'username'
                },
                {
                    data: 'role'
                },
                {
                    data: 'limit'
                },
                {
                    data: 'action'
                }
            ],
            btn: [{
                text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New</span>',
                className: 'is-button-add btn btn-primary me-2 ',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modal-add'
                }
            }],
        })

        $("#user-store").submit(function(e) {
            e.preventDefault()
            ilsya.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                addons_success: function() {
                    dbs.ajax.reload()
                    $("#user-store")[0].reset()
                    $("#modal-add").modal('hide')
                }
            })
        })

        $("#user-update").submit(function(e) {
            e.preventDefault()
            ilsya.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                addons_success: function() {
                    dbs.ajax.reload()
                    $("#user-update")[0].reset()
                    $("#modal-edit").modal('hide')
                }
            })
        })

        $(document).on('click', ".is-btn-user-edit", function() {
            var id = $(this).data('id')
            ilsya.ajax({
                type: "GET",
                url: "{{ route('admin.users.edit', '') }}" + "/" + id,
                success: function(res) {
                    let data = res.data
                    Swal.close()
                    $("#modal-edit").modal('show')
                    $("#modal-edit").find("input[name='id']").val(data.id)
                    $("#modal-edit").find("input[name='name']").val(data.name)
                    $("#modal-edit").find("input[name='username']").val(data.username)
                    $("#modal-edit").find("select[name='role']").val(data.role)
                    $("#modal-edit").find("input[name='limit_device']").val(data.limit_device)
                }
            })
        })

        $(document).on('click', ".is-btn-user-delete", function() {
            var id = $(this).data('id')
            Swal.fire({
                text: "You won't be able to revert this!",
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
                        url: "{{ route('admin.users.delete', '') }}" + "/" + id,
                        addons_success: function() {
                            dbs.ajax.reload()
                        }
                    })
                }
            })
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
