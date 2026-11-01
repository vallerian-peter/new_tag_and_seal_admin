<div class="mb-6 rounded-2xl border border-gray-200/90 bg-white p-5 shadow-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4 dark:border-gray-800/80">
        <div class="flex items-center gap-2.5">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/70 dark:text-primary-400">
                <x-heroicon-o-adjustments-horizontal class="h-5 w-5" style="width: 20px; height: 20px; max-width: 20px; max-height: 20px;" />
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Preview Context & Simulator Controls</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Simulate different field environments, user roles, language translations, and animal constraints.</p>
            </div>
        </div>

        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Live Sandbox (No Data Written)</span>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
        {{-- Language Selector --}}
        <div>
            <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                <x-heroicon-o-language class="h-4 w-4 text-primary-500" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                <span>Language / Lugha</span>
            </label>
            <div class="relative">
                <select
                    wire:model.live="previewLocale"
                    class="w-full appearance-none rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >
                    <option value="en">🇬🇧 English</option>
                    <option value="sw">🇹🇿 Kiswahili</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                    <x-heroicon-m-chevron-down class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                </div>
            </div>
        </div>

        {{-- Management Mode --}}
        <div>
            <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                <x-heroicon-o-rectangle-stack class="h-4 w-4 text-primary-500" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                <span>Management Mode</span>
            </label>
            <div class="relative">
                <select
                    wire:model.live="unitType"
                    class="w-full appearance-none rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >
                    <option value="individual">Individual animal</option>
                    <option value="group">Group / population</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                    <x-heroicon-m-chevron-down class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                </div>
            </div>
        </div>

        {{-- Animal Sex --}}
        <div>
            <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                <x-heroicon-o-user class="h-4 w-4 text-primary-500" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                <span>Animal Sex</span>
            </label>
            <div class="relative">
                <select
                    wire:model.live="sexCode"
                    class="w-full appearance-none rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >
                    @foreach(['female'=>'Female','male'=>'Male','unknown'=>'Unknown','intersex'=>'Intersex','not_applicable'=>'Not applicable'] as $code=>$name)
                        <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                    <x-heroicon-m-chevron-down class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                </div>
            </div>
        </div>

        {{-- Stage Code --}}
        <div>
            <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                <x-heroicon-o-tag class="h-4 w-4 text-primary-500" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                <span>Stage Code</span>
            </label>
            <input
                type="text"
                wire:model.live.debounce.250ms="stageCode"
                placeholder="e.g. lactating, heifer"
                class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-900 shadow-2xs transition-all placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
        </div>

        {{-- User Role --}}
        <div>
            <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                <x-heroicon-o-shield-check class="h-4 w-4 text-primary-500" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                <span>Simulated Role</span>
            </label>
            <div class="relative">
                <select
                    wire:model.live="previewRole"
                    class="w-full appearance-none rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >
                    @foreach(['farmer'=>'Farmer','extensionOfficer'=>'Extension officer','vet'=>'Veterinarian','farmInvitedUser'=>'Invited farm user'] as $code=>$name)
                        <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                    <x-heroicon-m-chevron-down class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                </div>
            </div>
        </div>
    </div>
</div>
