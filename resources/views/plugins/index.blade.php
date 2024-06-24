@extends('dash.layouts.app')

@section('title', 'Plugins')


@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Cmd</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection

@push('js')
    <script>
        var ilsya = new velixs()
        var dbs = ilsya.datatables({
            url: "{{ route('plugins.ajax') }}",
            header: `Plugins Installed`,
            columns: [{
                    data: 'responsive_id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'desc'
                },
                {
                    data: 'cmd'
                },
                {
                    data: 'status'
                },
                {
                    data: 'view'
                }
            ],
            btn: [{
                text: '<i class="ti ti-3d-cube-sphere me-sm-1" style="margin-top: -2px"></i> <span class="">More Plugins</span>',
                className: 'is-button-add btn btn-primary me-2 ',
            }],
        })

        $(document).on('click', ".is-change-status", function() {
            let command_name = $(this).data('name')
            let status = $(this).data('status')
            ilsya.ajax({
                type: "POST",
                url: "{{ route('plugins.change') }}",
                data: {
                    commands_name: command_name,
                    status: status
                },
                addons_success: function() {
                    dbs.ajax.reload()
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
