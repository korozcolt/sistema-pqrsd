<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            @forelse($comments as $comment)
                <div class="p-4 bg-white rounded-lg shadow {{ $comment->is_internal ? 'border-l-4 border-warning-500' : '' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-gray-900">
                                {{ $comment->user->name }}
                                @if($comment->is_internal)
                                    <span class="ml-2 text-sm text-warning-600">(Internal Note)</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $comment->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 prose max-w-none">
                        {!! $comment->content !!}
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">
                    No responses yet
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
