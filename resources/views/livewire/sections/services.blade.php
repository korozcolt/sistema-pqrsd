{{-- resources/views/livewire/sections/services.blade.php --}}
<div>
<section class="py-16 {{ $settings['background'] === 'white' ? 'bg-white' : 'bg-gray-50' }}">
    <div class="container mx-auto px-4">
        @if($title || $description)
        <div class="text-center max-w-3xl mx-auto mb-12">
            @if($title)
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
            @endif

            @if($description)
            <div class="text-gray-600">{!! $description !!}</div>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $gridColumns }} gap-8">
            @foreach($services as $service)
            <div class="{{ $cardClasses }}">
                @if($settings['show_icons'] && isset($service['icon']))
                <div class="text-primary-600 text-3xl mb-4">
                    <i class="{{ $service['icon'] }}"></i>
                </div>
                @endif

                <h3 class="text-xl font-semibold mb-3">{{ $service['title'] }}</h3>
                <div class="text-gray-600">{!! $service['description'] !!}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
</div>
