<div>
    {{-- resources/views/livewire/sections/features.blade.php --}}
    <section class="py-16 {{ $settings['background'] === 'gray' ? 'bg-gray-50' : 'bg-white' }}">
        <div class="container mx-auto px-4">
            @if ($title || $subtitle)
                <div class="text-center mb-12">
                    @if ($title)
                        <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
                    @endif
                    @if ($subtitle)
                        <p class="text-xl text-gray-600">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $settings['columns'] }} gap-8">
                @foreach ($features as $feature)
                    <div class="{{ $featureClasses }} transition duration-300 hover:transform hover:-translate-y-1">
                        @if ($settings['show_images'] && isset($feature['image']))
                            <img src="{{ Storage::url($feature['image']) }}" alt="{{ $feature['title'] }}"
                                class="w-16 h-16 object-cover mb-4 rounded">
                        @elseif(isset($feature['icon']))
                            <i class="{{ $feature['icon'] }} text-4xl text-primary-600 mb-4"></i>
                        @endif

                        <h3 class="text-xl font-semibold mb-3">{{ $feature['title'] }}</h3>
                        <p class="text-gray-600">{{ $feature['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
