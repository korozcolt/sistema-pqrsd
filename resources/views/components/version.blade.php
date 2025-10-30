<div class="text-xs text-gray-500 dark:text-gray-400">
    Sistema PQRSD
    <span class="font-semibold">v{{ config('version.version') }}</span>
    @if(config('version.release_date'))
        · {{ config('version.release_date') }}
    @endif
</div>
