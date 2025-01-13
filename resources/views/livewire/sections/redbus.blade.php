{{-- resources/views/livewire/sections/redbus.blade.php --}}
<div>
    <div class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex justify-{{ $position }}">
                <div class="w-full max-w-3xl {{ $containerClass }} rounded-lg bg-white p-{{ $padding }}">
                    <div class="widget" data-widgetid="{{ $widgetId }}"></div>
                </div>
            </div>
        </div>
    </div>
</div>
