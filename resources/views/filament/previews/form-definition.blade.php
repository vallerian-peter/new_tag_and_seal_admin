<x-filament-panels::page>
    @include('filament.previews.context')

    <div class="rounded-2xl border border-gray-200/90 bg-white p-6 shadow-xs dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5 dark:border-gray-800">
            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                    <x-heroicon-o-document-text class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Form Definition Sandbox</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Interactive preview of configured form fields, validation constraints, and translations.</p>
                </div>
            </div>
            <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                Single Form Sandbox
            </span>
        </div>

        @include('filament.previews.form')
    </div>
</x-filament-panels::page>
