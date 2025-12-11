<div>
    <!-- Back to Home button - fixed position outside card -->
    <a
        href="/"
        style="font-size: 14px; display: flex; flex-direction: row; align-items: center; gap: 10px; color: #525252;"
        class="font-medium"
    >
        <x-filament::icon
            icon="heroicon-o-chevron-left"
            class="w-4 h-4"
        />
        <span>Back to Home</span>
    </a>

    <!-- Login Card -->
    <x-filament-panels::page.simple>
        <form wire:submit="authenticate">
            {!! $this->form->toHtml($this) !!}

            <x-filament::button
                type="submit"
                size="xl"
                class="mt-2"
style="width: 100%; margin-top: 10px;"
            >
                Sign in
            </x-filament::button>
        </form>
    </x-filament-panels::page.simple>
</div>

