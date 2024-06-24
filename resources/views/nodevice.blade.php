@extends('dash.layouts.app')

@section('title', 'DEVICE 404')


@section('content')

    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <div class="misc-wrapper">
                    <h4 class="mb-1 mx-2">Select Device</h4>
                    <p class="mb-4 mx-2">Select the device first to continue</p>
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 col-xl-6 col-lg-6">
                            <select class="form-control main-device" style="cursor: pointer;">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
