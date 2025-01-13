@php
    $config = json_decode($section->config);
@endphp

@if(isset($config->title) || isset($config->subtitle))
    <div class="section-title">
        @if(isset($config->subtitle))
            <span>{{ $config->subtitle }}</span>
        @endif
        @if(isset($config->title))
            <h2>{{ $config->title }}</h2>
        @endif
    </div>
@endif

<div class="about-area pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="about-contant">
                    @if(isset($config->content))
                        <div class="about-text">
                            <p>{{ $config->content }}</p>
                            @if(isset($config->description))
                                <p>{{ $config->description }}</p>
                            @endif
                            @if(isset($config->button_text) && isset($config->button_url))
                                <a href="{{ $config->button_url }}" class="default-btn-one btn-bs">
                                    {{ $config->button_text }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($config->tabs))
                <div class="col-lg-3">
                    <div class="about-tabs">
                        <div class="tab-contant">
                            <h2 class="title">¡Torcoroma somos todos!</h2>
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach($config->tabs as $index => $tab)
                                        <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                           id="nav-{{ Str::slug($tab->title) }}-tab"
                                           data-bs-toggle="tab"
                                           href="#nav-{{ Str::slug($tab->title) }}"
                                           role="tab"
                                           aria-controls="nav-{{ Str::slug($tab->title) }}"
                                           aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                            {{ $tab->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </nav>

                            <div class="tab-content" id="nav-tabContent">
                                @foreach($config->tabs as $index => $tab)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                         id="nav-{{ Str::slug($tab->title) }}"
                                         role="tabpanel"
                                         aria-labelledby="nav-{{ Str::slug($tab->title) }}-tab">
                                        <div class="vision">
                                            <ul>
                                                @foreach($tab->content as $item)
                                                    <li>
                                                        <i class='bx bx-check'></i>
                                                        {{ $item }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
