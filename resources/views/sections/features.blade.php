@php
    $config = json_decode($section->config);
    $items = $config->items ?? [];
@endphp

<div class="contact-area mb-85">
    <div class="container">
        <div class="contact-content">
            <div class="row">
                @foreach($items as $item)
                    <div class="col-lg-3 col-sm-6">
                        <div class="contact-card">
                            @if(isset($item->icon))
                                <i class='bx {{ $item->icon }}'></i>
                            @endif

                            @if(isset($item->title))
                                <h4>{{ $item->title }}</h4>
                            @endif

                            @if(isset($item->description))
                                <p>{{ $item->description }}</p>
                            @endif

                            @if(isset($item->links))
                                @foreach($item->links as $link)
                                    <p>
                                        <a href="{{ $link->url }}">{{ $link->text }}</a>
                                    </p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
