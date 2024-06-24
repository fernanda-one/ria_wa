@extends('dash.layouts.app')

@section('title', 'Files')


@section('content')
    <div id="body"></div>
@endsection

@push('js')
    <script src="{!! asset('assets/libvelixs/ilsya.files.js') !!}"></script>
    <script>
        var files = new FileManager({
            subfolder: "{{ $auth->id }}",
            base_url: "{{ route('ilsya.files.index') }}"
        });

        files.init({
            body: "#body",
            ismain: true,
        })
    </script>
@endpush
