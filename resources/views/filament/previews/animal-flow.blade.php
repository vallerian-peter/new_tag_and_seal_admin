<x-filament-panels::page>
    <style>
        svg { display: inline-block; vertical-align: middle; }
        svg:not([style*="width"]) { max-width: 1.25rem; max-height: 1.25rem; }
    </style>

    {{-- Simulation Context Controls --}}
    @include('filament.previews.context')

    {{-- Blueprint Hero Header --}}
    <div class="mb-6 rounded-2xl border border-gray-200/90 bg-white p-6 shadow-xs dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-100 text-primary-700 shadow-inner dark:bg-primary-950 dark:text-primary-300">
                    <x-heroicon-o-sparkles class="h-6 w-6" style="width: 24px; height: 24px; max-width: 24px; max-height: 24px;" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="text-xl font-extrabold text-gray-950 dark:text-white">{{ $flow['name'] }}</h2>
                        <span class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2.5 py-0.5 text-xs font-mono font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                            {{ $flow['code'] }}
                        </span>
                        <span class="inline-flex items-center rounded-lg bg-primary-50 px-2 py-0.5 text-xs font-bold text-primary-700 dark:bg-primary-950/80 dark:text-primary-300 border border-primary-200/60 dark:border-primary-800/60">
                            version {{ $flow['version'] }}
                        </span>
                    </div>

                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 max-w-3xl leading-relaxed">
                        Registration and event forms use the selected blueprint version. Flutter receives the published definitions through configuration sync.
                        A published new version updates event choices for existing animals in the same blueprint family. Registration and recorded event versions remain unchanged.
                    </p>

                    @if($flow['binding'])
                        <div class="mt-3 flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                            <x-heroicon-m-link class="h-4 w-4 text-primary-500" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                            <span>Connected to existing mobile catalog: type <strong>{{ $flow['binding']['livestockTypeId'] ?? '—' }}</strong>, species <strong>{{ $flow['binding']['speciesId'] ?? '—' }}</strong>.</span>
                        </div>
                    @endif

                    @if(!$flow['modeAllowed'])
                        <div class="mt-3 inline-flex items-center gap-2 rounded-xl bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            <x-heroicon-m-exclamation-triangle class="h-4 w-4 text-amber-600" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                            <span>This management mode is not enabled for this blueprint.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Two-Column Workspace --}}
    <div
        x-data="{
            activeTab: 'registration',
            viewMode: 'focus'
        }"
        class="grid grid-cols-1 gap-6 lg:grid-cols-12"
    >
        {{-- Left Column: Event & Flow Selector Navigation (4 cols) --}}
        <div class="space-y-4 lg:col-span-4">
            <div class="rounded-2xl border border-gray-200/90 bg-white p-4 shadow-xs dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Blueprint Flows</h3>
                    <div class="inline-flex p-0.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-[10px] font-semibold">
                        <button
                            type="button"
                            x-on:click="viewMode = 'focus'"
                            class="px-2 py-1 rounded-md transition-all cursor-pointer"
                            x-bind:class="viewMode === 'focus' ? 'bg-white text-gray-900 shadow-2xs dark:bg-gray-700 dark:text-white' : 'text-gray-500'"
                        >
                            Focus Flow
                        </button>
                        <button
                            type="button"
                            x-on:click="viewMode = 'all'"
                            class="px-2 py-1 rounded-md transition-all cursor-pointer"
                            x-bind:class="viewMode === 'all' ? 'bg-white text-gray-900 shadow-2xs dark:bg-gray-700 dark:text-white' : 'text-gray-500'"
                        >
                            View All
                        </button>
                    </div>
                </div>

                <div class="mt-3 space-y-2">
                    {{-- Registration Flow Item --}}
                    <button
                        type="button"
                        x-on:click="activeTab = 'registration'"
                        class="w-full text-left rounded-xl p-3 border transition-all cursor-pointer flex items-center justify-between"
                        x-bind:class="activeTab === 'registration'
                            ? 'bg-primary-50/70 border-primary-300 text-primary-950 dark:bg-primary-950/40 dark:border-primary-700 dark:text-primary-100 shadow-2xs ring-1 ring-primary-400/30'
                            : 'bg-white border-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:hover:bg-gray-800/60'"
                    >
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
                                <x-heroicon-o-document-text class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white">Registration form</h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Animal profile intake</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-primary-100 px-2 py-0.5 text-[10px] font-bold text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                            Intake
                        </span>
                    </button>

                    <div class="pt-3 pb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Operational Events ({{ count($flow['events']) }})</span>
                    </div>

                    {{-- Event Items Loop --}}
                    @forelse($flow['events'] as $event)
                        <button
                            type="button"
                            x-on:click="activeTab = 'event-{{ $event['uuid'] }}'"
                            class="w-full text-left rounded-xl p-3 border transition-all cursor-pointer flex items-center justify-between"
                            x-bind:class="activeTab === 'event-{{ $event['uuid'] }}'
                                ? 'bg-primary-50/70 border-primary-300 text-primary-950 dark:bg-primary-950/40 dark:border-primary-700 dark:text-primary-100 shadow-2xs ring-1 ring-primary-400/30'
                                : 'bg-white border-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:hover:bg-gray-800/60'"
                        >
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $event['eligible'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                                    @if($event['eligible'])
                                        <x-heroicon-o-check class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                    @else
                                        <x-heroicon-o-lock-closed class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">{{ $event['name'] }}</h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-mono">{{ $event['behavior'] ?? 'custom_event' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                @if($event['eligible'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/50">
                                        Eligible
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/50">
                                        Restricted
                                    </span>
                                @endif
                            </div>
                        </button>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-200 p-4 text-center text-xs text-gray-400">
                            No events configured for this blueprint version.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Column: Interactive Mobile Simulator Screen (8 cols) --}}
        <div class="space-y-6 lg:col-span-8">
            {{-- Registration Form Presentation --}}
            <div
                x-show="viewMode === 'all' || activeTab === 'registration'"
                x-transition
                class="rounded-2xl border border-gray-200/90 bg-white p-6 shadow-xs dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5 dark:border-gray-800">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                            <x-heroicon-o-clipboard-document-list class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Registration form</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mobile profile intake schema</p>
                        </div>
                    </div>
                    <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        Initial Enrollment
                    </span>
                </div>

                @if($flow['registration'])
                    @include('filament.previews.form', ['form' => $flow['registration']])
                @else
                    <div class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-xs text-gray-500">
                        Registration form is unavailable.
                    </div>
                @endif
            </div>

            {{-- Operational Event Presentations Loop --}}
            @forelse($flow['events'] as $event)
                <div
                    id="event-{{ $event['uuid'] }}"
                    x-show="viewMode === 'all' || activeTab === 'event-{{ $event['uuid'] }}'"
                    x-transition
                    class="rounded-2xl border border-gray-200/90 bg-white p-6 shadow-xs dark:border-gray-800 dark:bg-gray-900"
                >
                    {{-- Event Header Details --}}
                    <div class="border-b border-gray-100 pb-4 mb-5 dark:border-gray-800">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $event['eligible'] ? 'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                                    <x-heroicon-o-bolt class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $event['name'] }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event['explanation'] }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="rounded-lg bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    Status: {{ $event['status'] }}
                                </span>
                                @if($event['eligible'])
                                    <span class="rounded-lg bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        Active in Context
                                    </span>
                                @else
                                    <span class="rounded-lg bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        Restricted by Rules
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <span>For bulk capture, select eligible animals and complete each animal’s fields. Every animal receives a separate event and synchronization result.</span>
                            @if($event['legacyLogType'])
                                <span class="inline-flex items-center gap-1 font-medium text-primary-600 dark:text-primary-400">
                                    <x-heroicon-m-arrow-path class="h-3.5 w-3.5" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px;" />
                                    Extends the existing {{ $event['legacyLogType'] }} action in animal details.
                                </span>
                            @else
                                <span>Appears as a configured event in the same animal-details area.</span>
                            @endif
                        </div>
                    </div>

                    {{-- Eligibility Check & Form Rendering --}}
                    @if(!$event['eligible'])
                        <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-5 text-amber-800 dark:border-amber-800/80 dark:bg-amber-950/40 dark:text-amber-300">
                            <div class="flex items-start gap-3">
                                <x-heroicon-m-shield-exclamation class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" style="width: 20px; height: 20px; max-width: 20px; max-height: 20px;" />
                                <div>
                                    <p class="text-sm font-bold text-amber-900 dark:text-amber-200">Not eligible for the selected animal context.</p>
                                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">This event's eligibility rule does not match the simulated sex (<strong>{{ $sexCode }}</strong>), management mode (<strong>{{ $unitType }}</strong>), or stage code. Adjust the simulation controls above to test this form.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($event['form'])
                        @include('filament.previews.form', ['form' => $event['form']])
                    @else
                        <div class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-xs text-gray-500">
                            Event form is unavailable.
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 p-8 text-center text-xs text-gray-500">
                    No events are configured for this blueprint version.
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
