{{-- resources/views/livewire/sections/hero.blade.php --}}
<div>
    <div class="relative {{ $fullHeight ? 'min-h-screen' : 'min-h-[600px]' }}">
        {{-- Background Image --}}
        @if ($background)
            <img src="{{ Storage::url($background) }}" alt="{{ $title }}"
                class="absolute inset-0 w-full h-full object-cover">
        @endif

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black" style="opacity: {{ $overlayOpacity / 100 }}"></div>

        {{-- Content --}}
        <div class="relative z-10 container mx-auto px-4 h-full flex items-center">
            <div class="max-w-4xl">
                @if ($title)
                    <h1 class="text-{{ $textColor }} text-4xl md:text-5xl lg:text-6xl font-bold mb-4">
                        {{ $title }}
                    </h1>
                @endif

                @if ($subtitle)
                    <p class="text-{{ $textColor }} text-xl md:text-2xl opacity-90 mb-8">
                        {{ $subtitle }}
                    </p>
                @endif

                @if ($buttonText && $buttonUrl)
                    <a href="{{ $buttonUrl }}"
                        class="inline-block bg-primary-600 text-white px-8 py-3 rounded-lg
                       hover:bg-primary-700 transition-colors duration-300">
                        {{ $buttonText }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
