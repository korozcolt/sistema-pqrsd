<div>
    {{-- resources/views/livewire/sections/stats.blade.php --}}
    <section class="py-16 {{ $settings['background'] === 'gray' ? 'bg-gray-50' : 'bg-white' }}">
        <div class="container mx-auto px-4">
            @if ($title || $description)
                <div class="text-center mb-12">
                    @if ($title)
                        <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
                    @endif
                    @if ($description)
                        <p class="text-xl text-gray-600">{{ $description }}</p>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-{{ $settings['columns'] }} gap-8">
                @foreach ($stats as $stat)
                    <div class="{{ $statClasses }} text-center">
                        @if (isset($stat['icon']))
                            <i class="{{ $stat['icon'] }} text-3xl text-primary-600 mb-4"></i>
                        @endif

                        <div class="text-4xl font-bold text-gray-900 mb-2"
                            @if ($settings['animate']) x-data="{ value: 0 }"
                         x-intersect="$el.innerHTML = {{ $stat['value'] }}"
                         @else
                         >{{ $stat['value'] }} @endif
                            </div>

                            <div class="text-lg font-medium text-gray-600">{{ $stat['label'] }}</div>

                            @if (isset($stat['description']))
                                <p class="mt-2 text-gray-500 text-sm">{{ $stat['description'] }}</p>
                            @endif
                        </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
