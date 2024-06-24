<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{!! asset('assets') !!}/" data-template="vertical-menu-template">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base_url" content="{{ asset('') }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>PHB - {{ \Str::ucfirst(\Str::lower($__env->yieldContent('title'))) }}</title>
    <link rel="icon" type="image/x-icon" href="{!! asset('assets') !!}/img/favicon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/css/rtl/core.css?v=12" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/css/demo.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/spinkit/spinkit.css" />
    @stack('cssvendor')
    <script src="{!! asset('assets') !!}/vendor/js/helpers.js"></script>
    <script src="{!! asset('assets') !!}/vendor/js/template-customizer.js"></script>
    <script src="{!! asset('assets') !!}/js/config.js"></script>
    @stack('css')
    <style>
        .menu-icon {
            font-size: 1.475rem;
        }

        /* width */
        ::-webkit-scrollbar {
            width: 4px;
            height: 3px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    <script src="{!! asset('assets/libvelixs/velixs.js') !!}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('dash.layouts.menu')
            <div class="layout-page">
                @include('dash.layouts.header')
                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    <script src="{!! asset('assets') !!}/vendor/libs/jquery/jquery.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/popper/popper.js"></script>
    <script src="{!! asset('assets') !!}/vendor/js/bootstrap.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/node-waves/node-waves.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/hammer/hammer.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/i18n/i18n.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    @stack('jsvendor')
    <script src="{!! asset('assets') !!}/vendor/js/menu.js"></script>
    <script src="{!! asset('assets') !!}/js/main.js"></script>
    <script>
        $(".main-device").on('change', function(e) {
            var device = $(this).val();
            new velixs().ajax({
                url: "{{ route('ajax.change_device') }}",
                data: {
                    device: device,
                },
                addons_success: function({
                    swal
                }) {
                    swal.then(() => {
                        location.reload();
                    })
                },
            })
        });

        function main_device() {
            new velixs().ajax({
                url: "{{ route('ajax.main_device') }}",
                data: {
                    main_device_id: '{{ $main_device ? $main_device->id : null }}',
                },
                success: function(data) {
                    $(".main-device").html(data);
                },
                beforeSend: function() {
                    $(".main-device").html('<option value="">Loading...</option>');
                },
            })
        }

        main_device();
    </script>
    @if ($errors->any())
        <script>
            (function() {
                Swal.fire({
                    text: '{{ $errors->first() }}',
                    icon: 'error',
                    showClass: {
                        popup: 'animate__animated animate__shakeX'
                    },
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                })
            })()
        </script>
    @endif
    @stack('js')
</body>

</html>
