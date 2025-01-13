@extends('layouts.page')

@section('content-page')
    @foreach($page->sections as $section)
        @if($section->is_active)
            @php
                echo Blade::render($section->content, ['info' => $info]);
            @endphp
        @endif
    @endforeach
@endsection

@push('scripts')
    @if(env('APP_ENV') == 'production')
        <script defer type="text/javascript" src="https://wl.redbus.com/javascripts/widget.min.js"></script>
        <script src="https://wl.redbus.com/externaljavascript/loadwidget.js"></script>
    @endif
@endpush
