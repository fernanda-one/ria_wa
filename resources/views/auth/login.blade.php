<!DOCTYPE html>

<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{!! asset('assets') !!}/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>LOGIN - LAZY GATEWAY</title>

    <meta name="description" content="LAZY GATEWAY" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{!! asset('assets') !!}/img/favicon.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/fonts/tabler-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/sweetalert2/sweetalert2.css" />
    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="{!! asset('assets') !!}/vendor/js/helpers.js"></script>
    <script src="{!! asset('assets') !!}/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{!! asset('assets') !!}/js/config.js"></script>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Login -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="#" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo" style="height: unset;">
                                    <img style="height: 30px" src="{!! asset('assets/img/logo.png') !!}" alt="walix">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1 pt-2">Welcome to WALIX! 👋</h4>
                        <p class="mb-4">Please sign-in to your account!</p>

                        <form class="mb-3" action="{!! route('login') !!}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Username</label>
                                <input type="text" class="form-control" value="{{ config('app.isdemo') ? 'admin' : '' }}" name="username" placeholder="Enter your username" autofocus required />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" value="{{ config('app.isdemo') ? 'admin' : '' }}" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" />
                                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>

                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{!! asset('assets') !!}/vendor/libs/jquery/jquery.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/popper/popper.js"></script>
    <script src="{!! asset('assets') !!}/vendor/js/bootstrap.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/node-waves/node-waves.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/hammer/hammer.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/i18n/i18n.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="{!! asset('assets') !!}/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{!! asset('assets') !!}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{!! asset('assets') !!}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Main JS -->
    <script src="{!! asset('assets') !!}/js/main.js"></script>

    @if ($errors->any())
        <script>
            Swal.fire({
                text: '{{ $errors->first() }}',
                icon: 'error',
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            })
        </script>
    @endif
</body>

</html>
