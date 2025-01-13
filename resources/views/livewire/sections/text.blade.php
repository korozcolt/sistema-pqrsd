{{-- resources/views/livewire/sections/text.blade.php --}}
<div>
    <section class="{{ $paddingClass }} {{ $background === 'gray' ? 'bg-gray-50' : 'bg-white' }}">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                @if ($title)
                    <h2 class="text-3xl font-bold mb-8 text-{{ $alignment }}">{{ $title }}</h2>
                @endif

                <div class="prose max-w-none {{ $columnClass }} text-{{ $alignment }}">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </section>
</div>
