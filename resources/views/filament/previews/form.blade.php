<div class="space-y-6" wire:key="preview-{{ $form['key'] }}">
    {{-- Form Header --}}
    <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-gray-800/50">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $form['title'] }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $form['description'] }}</p>
            </div>
            <div class="inline-flex items-center gap-1.5 rounded-lg bg-gray-200/80 px-2.5 py-1 text-[11px] font-mono font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                <span>{{ $form['code'] }}</span>
                <span>·</span>
                <span>v{{ $form['version'] }}</span>
            </div>
        </div>
    </div>

    {{-- Form Sections --}}
    @foreach($form['sections'] as $section)
        <div class="space-y-4 rounded-xl border border-gray-200/90 bg-white p-5 shadow-2xs dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3 dark:border-gray-800">
                <span class="h-4 w-1 rounded-full bg-primary-500"></span>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">{{ $section['label'] }}</h4>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @foreach($section['fields'] as $field)
                    @php($path = 'previewValues.'.$form['key'].'.'.$field['key'])
                    <div class="space-y-1.5" wire:key="{{ $form['key'] }}-{{ $field['key'] }}">
                        <div class="flex flex-wrap items-center justify-between gap-1.5">
                            <label for="{{ $form['key'] }}-{{ $field['key'] }}" class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                <span>{{ $field['label'] }}</span>
                                @if($field['required'])
                                    <span class="text-danger-500 font-bold" aria-label="required">*</span>
                                @endif
                            </label>

                            @if($field['role'])
                                <span class="inline-flex items-center gap-1 rounded-md bg-sky-50 px-2 py-0.5 text-[10px] font-semibold text-sky-700 border border-sky-200/60 dark:bg-sky-950/50 dark:border-sky-800/60 dark:text-sky-300" title="Behaviour input">
                                    <x-heroicon-m-sparkles class="h-3 w-3" style="width: 12px; height: 12px; max-width: 12px; max-height: 12px;" />
                                    <span>{{ $field['role'] }}</span>
                                </span>
                            @endif
                        </div>

                        {{-- Input Elements by Component Type --}}
                        @if($field['binding'])
                            <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50/80 px-3 py-2 text-xs text-gray-600 dark:border-gray-800 dark:bg-gray-800/60 dark:text-gray-400">
                                <x-heroicon-m-link class="h-4 w-4 text-primary-500 shrink-0" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                <span>Value supplied by the existing input: <strong>{{ $field['binding'] }}</strong></span>
                            </div>

                        @elseif($field['component'] === 'boolean')
                            <div id="{{ $form['key'] }}-{{ $field['key'] }}" role="radiogroup" class="grid grid-cols-2 gap-2">
                                <label class="flex items-center justify-center gap-2 rounded-xl border border-gray-200 p-2.5 text-xs font-semibold text-gray-700 transition-all hover:bg-gray-50 cursor-pointer dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                    <input type="radio" value="1" wire:model.live="{{ $path }}" class="text-primary-600 focus:ring-primary-500" />
                                    <span>Yes</span>
                                </label>
                                <label class="flex items-center justify-center gap-2 rounded-xl border border-gray-200 p-2.5 text-xs font-semibold text-gray-700 transition-all hover:bg-gray-50 cursor-pointer dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                    <input type="radio" value="0" wire:model.live="{{ $path }}" class="text-primary-600 focus:ring-primary-500" />
                                    <span>No</span>
                                </label>
                            </div>

                        @elseif($field['component'] === 'measurement')
                            <div class="relative flex items-center">
                                <input
                                    id="{{ $form['key'] }}-{{ $field['key'] }}"
                                    type="number"
                                    step="any"
                                    wire:model.live.number="{{ $path }}"
                                    placeholder="{{ $field['placeholder'] }}"
                                    class="w-full rounded-xl border border-gray-300 bg-white py-2 pl-3 pr-14 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                />
                                @if($field['unit'])<span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs font-bold text-primary-600 dark:text-primary-400">{{ $field['unit'] }}</span>@endif
                            </div>

                        @elseif($field['component'] === 'identifier_scanner')
                            <div class="relative flex items-center">
                                <input
                                    id="{{ $form['key'] }}-{{ $field['key'] }}"
                                    type="text"
                                    wire:model.live.debounce.250ms="{{ $path }}"
                                    placeholder="{{ $field['placeholder'] }}"
                                    class="w-full rounded-xl border border-gray-300 bg-white py-2 pl-3 pr-10 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                />
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400">
                                    <x-heroicon-o-qr-code class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">On mobile: scan a QR code, barcode or Bluetooth RFID tag, or type it.</p>

                        @elseif($field['component'] === 'location')
                            <div id="{{ $form['key'] }}-{{ $field['key'] }}" class="flex items-center gap-3 rounded-xl border border-dashed border-gray-300 bg-gray-50/60 p-3.5 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-800/40 dark:text-gray-400">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                    <x-heroicon-o-map-pin class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">GPS Position Capture</p>
                                    <p class="text-[11px] text-gray-500">On mobile: use current GPS position or choose a point on the map. Saved as latitude and longitude.</p>
                                </div>
                            </div>

                        @elseif($field['component'] === 'subject_reference')
                            <div class="relative flex items-center">
                                <input
                                    id="{{ $form['key'] }}-{{ $field['key'] }}"
                                    type="text"
                                    disabled
                                    placeholder="Choose {{ ($field['reference']['multiple'] ?? false) ? 'animals' : 'an animal' }} from the farm"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2 pl-3 pr-10 text-xs text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                                />
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400">
                                    <x-heroicon-o-magnifying-glass class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">On mobile: pick from {{ ($field['reference']['same_species'] ?? true) ? 'animals of the same species' : 'any animal' }} on the farm @if(!empty($field['reference']['sex_codes']))({{ implode(', ', $field['reference']['sex_codes']) }})@endif.</p>

                        @elseif($field['component'] === 'repeatable_group')
                            <div id="{{ $form['key'] }}-{{ $field['key'] }}" class="space-y-2 rounded-xl border border-dashed border-gray-300 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/40">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-list-bullet class="h-4 w-4 text-primary-500" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">Each entry asks for:</p>
                                </div>
                                <ul class="grid grid-cols-1 gap-1.5 sm:grid-cols-2 text-xs">
                                    @foreach($field['items'] as $item)
                                        <li class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-primary-400"></span>
                                            <span>{{ $item['label'] }}@if($item['unit']) ({{ $item['unit'] }})@endif @if($item['required'])*@endif</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 border-t border-gray-200/60 pt-2 dark:border-gray-700/60">On mobile: add, edit and remove entries dynamically.</p>
                            </div>

                        @elseif($field['component'] === 'photo')
                            <div id="{{ $form['key'] }}-{{ $field['key'] }}" class="flex items-center gap-3 rounded-xl border border-dashed border-gray-300 bg-gray-50/60 p-3.5 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-800/40 dark:text-gray-400">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                                    <x-heroicon-o-camera class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">Photo Attachment</p>
                                    <p class="text-[11px] text-gray-500">On mobile: take or choose a photo. It uploads when the device is online.</p>
                                </div>
                            </div>

                        @elseif(in_array($field['component'], ['single_select','multi_select'], true))
                            <div class="relative">
                                <select
                                    id="{{ $form['key'] }}-{{ $field['key'] }}"
                                    wire:model.live="{{ $path }}"
                                    @if($field['component']==='multi_select') multiple @endif
                                    class="w-full appearance-none rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                >
                                    @if($field['component']==='single_select')
                                        <option value="">Select option</option>
                                    @endif
                                    @foreach($field['options'] as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <x-heroicon-m-chevron-down class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
                                </div>
                            </div>

                        @elseif($field['component'] === 'multiline_text')
                            <textarea
                                id="{{ $form['key'] }}-{{ $field['key'] }}"
                                wire:model.live.debounce.250ms="{{ $path }}"
                                rows="3"
                                placeholder="{{ $field['placeholder'] }}"
                                class="w-full rounded-xl border border-gray-300 bg-white p-3 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            ></textarea>

                        @elseif(in_array($field['component'], ['text','integer','decimal','date','date_time'], true))
                            <input
                                id="{{ $form['key'] }}-{{ $field['key'] }}"
                                type="{{ match($field['component']) {'integer','decimal'=>'number','date'=>'date','date_time'=>'datetime-local',default=>'text'} }}"
                                @if(in_array($field['component'], ['integer','decimal']))
                                    wire:model.live.number="{{ $path }}"
                                    step="{{ $field['component']==='integer'?'1':'any' }}"
                                @else
                                    wire:model.live.debounce.250ms="{{ $path }}"
                                @endif
                                placeholder="{{ $field['placeholder'] }}"
                                class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-900 shadow-2xs transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            />

                        @else
                            <div class="rounded-xl bg-amber-50 p-3 text-xs text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                This input type is not yet available on mobile, so this form cannot be published.
                            </div>
                        @endif

                        {{-- Field Guidance & Range Constraints --}}
                        @if($field['help'])
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $field['help'] }}</p>
                        @endif
                        @if(isset($field['validation']['min']) || isset($field['validation']['max']))
                            <p class="text-[11px] text-gray-400">Allowed range: {{ $field['validation']['min'] ?? '—' }} to {{ $field['validation']['max'] ?? '—' }}</p>
                        @endif
                        @if(isset($field['validation']['min_length']) || isset($field['validation']['max_length']))
                            <p class="text-[11px] text-gray-400">Allowed length: {{ $field['validation']['min_length'] ?? 0 }} to {{ $field['validation']['max_length'] ?? '—' }} characters</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    {{-- Simulated Submit Action --}}
    <div class="pt-2">
        <button
            type="button"
            disabled
            class="w-full flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-3 text-xs font-bold text-white shadow-sm opacity-90 cursor-not-allowed dark:bg-primary-500"
        >
            <x-heroicon-m-check-circle class="h-4 w-4" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" />
            <span>{{ $form['submitLabel'] }} — preview only</span>
        </button>
    </div>
</div>
