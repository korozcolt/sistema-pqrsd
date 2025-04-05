@extends('layouts.page')

@section('content-page')
{{-- Debug information --}}
@if(config('app.debug'))
<div class="container mt-4">
    <div class="alert alert-info">
        <p><strong>Page:</strong> {{ $page->title }}</p>
        <p><strong>Sections Count:</strong> {{ $page->sections->count() }}</p>
        <pre>{{ print_r($page->sections->toArray(), true) }}</pre>
    </div>
</div>
@endif
    @foreach($page->sections->sortBy('order') as $section)
        <div class="section {{ $section->position }}-section">
            @include($section->type->getViewComponent(), ['section' => $section])
        </div>
    @endforeach
@endsection
