@php
    $config = json_decode($section->config);
    $slides = $config->slides ?? [];
@endphp

<div class="hero-slider owl-carousel owl-theme">
    @foreach($slides as $slide)
        <div class="hero-slider-item {{ $slide->background }}">
            <div class="d-table">
                <div class="d-table-cell">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="">
                                <div class="container">
                                    <div class="banner-content">
                                        @if(isset($slide->title))
                                            <h1>{{ $slide->title }}</h1>
                                        @endif

                                        @if(isset($slide->subtitle))
                                            <h2>{{ $slide->subtitle }}</h2>
                                        @endif

                                        @if(isset($slide->description))
                                            <p>{{ $slide->description }}</p>
                                        @endif

                                        @if(isset($slide->button_text) && isset($slide->button_url))
                                            <a href="{{ $slide->button_url }}" class="default-btn-one">
                                                {{ $slide->button_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
