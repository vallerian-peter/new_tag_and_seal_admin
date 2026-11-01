@php($viewUrl = fn ($animal) => \App\Filament\Resources\AnimalSubjects\AnimalSubjectResource::getUrl('view', ['record' => $animal->id]))
<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-1">
            <x-filament::section heading="Profile">
                <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                    <dt class="text-gray-500">Species</dt><dd class="text-right font-medium">{{ $subject->species?->name ?? '—' }}</dd>
                    <dt class="text-gray-500">Farm</dt><dd class="text-right font-medium">{{ $subject->farm?->name ?? $subject->farm_uuid }}</dd>
                    <dt class="text-gray-500">Mode</dt><dd class="text-right font-medium">{{ $subject->unit_type === 'group' ? 'Group' : 'Individual' }}</dd>
                    @if($subject->unit_type === 'group')
                        <dt class="text-gray-500">Quantity</dt><dd class="text-right font-medium">{{ number_format((int) $subject->quantity) }}</dd>
                    @else
                        <dt class="text-gray-500">Sex</dt><dd class="text-right font-medium">{{ str($subject->sex_code ?? 'unknown')->headline() }}</dd>
                    @endif
                    <dt class="text-gray-500">Born / started</dt><dd class="text-right font-medium">{{ $subject->birth_or_start_date?->format('j M Y') ?? '—' }}</dd>
                    <dt class="text-gray-500">Stage</dt><dd class="text-right font-medium">{{ $subject->current_stage_code ? str($subject->current_stage_code)->headline() : '—' }}</dd>
                    <dt class="text-gray-500">Status</dt>
                    <dd class="text-right">
                        <x-filament::badge :color="$subject->trashed() || $subject->lifecycle_status === 'archived' ? 'danger' : 'success'" class="inline-flex">
                            {{ $subject->trashed() || $subject->lifecycle_status === 'archived' ? 'Archived' : 'Active' }}
                        </x-filament::badge>
                    </dd>
                    @if(isset($subject->attributes_json['weight']))
                        <dt class="text-gray-500">Weight</dt><dd class="text-right font-medium">{{ $subject->attributes_json['weight'] }} kg</dd>
                    @endif
                    <dt class="text-gray-500">Revision</dt><dd class="text-right font-medium">{{ $subject->revision }}</dd>
                    <dt class="text-gray-500">Origin / Source</dt>
                    <dd class="text-right font-medium">
                        {{ $legacy ? 'Upgraded from legacy #'.$legacy->source_key : 'Native Platform' }}
                    </dd>
                    @if($subject->trashed())
                        <dt class="text-danger-500">Archived At</dt>
                        <dd class="text-right font-medium text-danger-600">{{ $subject->deleted_at?->format('j M Y, h:i A') ?? '—' }}</dd>
                        <dt class="text-danger-500">Archive Reason</dt>
                        <dd class="text-right font-medium text-danger-600">{{ $subject->delete_reason ?: 'No reason recorded' }}</dd>
                    @endif
                </dl>
            </x-filament::section>

            <x-filament::section heading="Identifiers">
                @forelse($identifiers as $identifier)
                    <div class="flex items-center justify-between gap-3 py-1 text-sm">
                        <span class="text-gray-500">{{ $identifier->type ?? 'Identifier' }}</span>
                        <span class="font-medium @if($identifier->status !== 'active') line-through text-gray-400 @endif">{{ $identifier->display_value }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No identifiers recorded.</p>
                @endforelse
            </x-filament::section>

            <x-filament::section heading="Family">
                <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                    <dt class="text-gray-500">Mother</dt>
                    <dd class="text-right font-medium">@if($mother)<a class="text-primary-600 hover:underline" href="{{ $viewUrl($mother) }}">{{ $mother->display_name ?: 'Unnamed' }}</a>@else — @endif</dd>
                    <dt class="text-gray-500">Father</dt>
                    <dd class="text-right font-medium">@if($father)<a class="text-primary-600 hover:underline" href="{{ $viewUrl($father) }}">{{ $father->display_name ?: 'Unnamed' }}</a>@else — @endif</dd>
                </dl>
                @if($offspring->isNotEmpty())
                    <p class="mt-4 text-xs font-medium uppercase tracking-wide text-gray-500">Offspring ({{ $offspring->count() }})</p>
                    <ul class="mt-2 space-y-1 text-sm">
                        @foreach($offspring as $child)
                            <li class="flex justify-between gap-3"><a class="text-primary-600 hover:underline" href="{{ $viewUrl($child) }}">{{ $child->display_name ?: 'Unnamed' }}</a><span class="text-gray-500">{{ str($child->sex_code)->headline() }} · {{ $child->birth_or_start_date?->format('j M Y') }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </x-filament::section>

            @if($reproduction)
                <x-filament::section heading="Reproduction">
                    <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                        <dt class="text-gray-500">Births</dt><dd class="text-right font-medium">{{ $reproduction->births_count }}</dd>
                        <dt class="text-gray-500">Born alive</dt><dd class="text-right font-medium">{{ $reproduction->total_born_alive }}</dd>
                        <dt class="text-gray-500">Born dead</dt><dd class="text-right font-medium">{{ $reproduction->total_born_dead }}</dd>
                        <dt class="text-gray-500">Last birth</dt><dd class="text-right font-medium">{{ $reproduction->last_birth_at ? \Carbon\Carbon::parse($reproduction->last_birth_at)->format('j M Y') : '—' }} ({{ $reproduction->last_born_alive }} alive)</dd>
                    </dl>
                </x-filament::section>
            @endif

            @if($production->isNotEmpty())
                <x-filament::section heading="Production — last 30 days">
                    @foreach($production as $product)
                        <div class="flex justify-between gap-3 py-1 text-sm">
                            <span class="text-gray-500">{{ str($product->product_code)->headline() }}</span>
                            <span class="font-medium">{{ rtrim(rtrim(number_format((float) $product->quantity, 3, '.', ','), '0'), '.') }} {{ $product->unit }}@if((float) $product->damaged > 0) <span class="text-gray-500">({{ rtrim(rtrim(number_format((float) $product->damaged, 3, '.', ','), '0'), '.') }} damaged)</span>@endif</span>
                        </div>
                    @endforeach
                </x-filament::section>
            @endif
        </div>

        <div class="lg:col-span-2">
            <x-filament::section heading="Timeline" description="Every saved event for this animal, newest first.">
                <x-slot name="afterHeader">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model.live="showVoided" class="rounded border-gray-300" /> Show voided</label>
                </x-slot>
                <ol class="relative space-y-4 border-s border-gray-200 ps-6 dark:border-gray-700" data-timeline>
                    @forelse($events as $event)
                        <li class="relative" wire:key="event-{{ $event['uuid'] }}">
                            <span class="absolute -start-[31px] top-1.5 h-3 w-3 rounded-full {{ $event['voided'] ? 'bg-gray-300' : 'bg-primary-500' }} ring-4 ring-white dark:ring-gray-900"></span>
                            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700 {{ $event['voided'] ? 'opacity-60' : '' }}">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h4 class="font-semibold @if($event['voided']) line-through @endif">{{ $event['name'] }}</h4>
                                    <div class="flex items-center gap-2">
                                        @if($event['family'])<x-filament::badge color="info">{{ $event['family'] }}</x-filament::badge>@endif
                                        @if($event['voided'])<x-filament::badge color="danger">Voided</x-filament::badge>@endif
                                        <span class="text-xs text-gray-500">{{ $event['occurredAt']?->format('j M Y H:i') }}</span>
                                    </div>
                                </div>
                                @if($event['rows'] !== [])
                                    <dl class="mt-3 grid gap-x-6 gap-y-1 text-sm sm:grid-cols-2">
                                        @foreach($event['rows'] as $row)
                                            <div class="flex justify-between gap-3"><dt class="font-medium text-gray-500">{{ $row['label'] }}</dt><dd class="text-right">{{ $row['value'] }}</dd></div>
                                        @endforeach
                                    </dl>
                                @endif
                                @if($event['voided'] && $event['voidReason'])<p class="mt-2 text-xs text-danger-600">Void reason: {{ $event['voidReason'] }}</p>@endif
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">No events recorded yet.</li>
                    @endforelse
                </ol>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
