<div>
    {{-- resources/views/livewire/sections/banner.blade.php --}}
    <section class="relative {{ $heightClass }} flex items-center">
        {{-- Background Image --}}
        @if ($background)
            <img src="{{ Storage::url($background) }}" alt="{{ $title }}"
                class="absolute inset-0 w-full h-full object-cover">
        @endif

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black" style="opacity: {{ $settings['overlay_opacity'] / 100 }}"></div>

        {{-- Content --}}
        <div class="relative z-10 container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-{{ $settings['text_alignment'] }}">
                @if ($title)
                    <h2 class="text-{{ $settings['text_color'] }} text-3xl md:text-4xl font-bold mb-4">
                        {{ $title }}
                    </h2>
                @endif

                @if ($description)
                    <p class="text-{{ $settings['text_color'] }} text-lg md:text-xl opacity-90 mb-8">
                        {{ $description }}
                    </p>
                @endif

                @if ($cta_text && $cta_url)
                    <a href="{{ $cta_url }}"
                        class="inline-block bg-primary-600 text-white px-8 py-3 rounded-lg
                           hover:bg-primary-700 transition duration-300">
                        {{ $cta_text }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
