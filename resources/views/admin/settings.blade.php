@extends('dash.layouts.app')

@section('title', 'SETTINGS')


@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Settings</h4>
            <div class="row">
                <div class="col-12 col-xl-6 col-lg-6 col-md-6">
                    <div class="mb-3">
                        <label for="">BASE NODE</label>
                        <input type="text" name="base_node" class="form-control" placeholder="https://localhost:5570" value="{{ config('app.base_node') }}">
                    </div>
                </div>
                <div class="col-12 col-xl-6 col-lg-6 col-md-6">
                    <div class="mb-3">
                        <label for="">INSTALL IN</label>
                        <select name="install_in">
                            <option value="localhost">Localhost</option>
                            <option value="hosting">Hosting</option>
                        </select>
                    </div>
                </div>
            </div>
            <a href="#" class="btn btn-primary">Go somewhere</a>
        </div>
    </div>
@endsection
