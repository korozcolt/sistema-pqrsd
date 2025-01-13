@extends('layouts.page')

@section('content-page')
    @foreach($page->sections->sortBy('order') as $section)
        <div class="section {{ $section->position }}-section">
            @include($section->type->getViewComponent(), ['section' => $section])
        </div>
    @endforeach
@endsection
