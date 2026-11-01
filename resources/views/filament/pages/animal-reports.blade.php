@php($fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, '.', ','), '0'), '.'))
<x-filament-panels::page>
    <x-filament::section>
        <div class="grid gap-4 sm:grid-cols-3">
            <label class="space-y-1 text-sm"><span class="font-medium">Farm</span>
                <select wire:model.live="farmUuid" class="w-full rounded-lg border-gray-300 bg-white dark:bg-gray-900">
                    <option value="">All farms</option>
                    @foreach($farms as $uuid => $name)<option value="{{ $uuid }}">{{ $name }}</option>@endforeach
                </select>
            </label>
            <label class="space-y-1 text-sm"><span class="font-medium">From</span><input type="date" wire:model.live="from" class="w-full rounded-lg border-gray-300 bg-white dark:bg-gray-900" /></label>
            <label class="space-y-1 text-sm"><span class="font-medium">To</span><input type="date" wire:model.live="to" class="w-full rounded-lg border-gray-300 bg-white dark:bg-gray-900" /></label>
        </div>
    </x-filament::section>

    <x-filament::tabs>
        @foreach(['animals' => 'Animals', 'events' => 'Events', 'reproduction' => 'Reproduction', 'production' => 'Production', 'health' => 'Health'] as $key => $label)
            <x-filament::tabs.item :active="$tab === $key" wire:click="setTab('{{ $key }}')">{{ $label }}</x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <x-filament::section>
        @if($tab === 'reproduction')
            <div class="mb-6 grid gap-4 sm:grid-cols-4">
                @foreach(['mothers' => 'Mothers', 'births' => 'Births', 'bornAlive' => 'Born alive', 'bornDead' => 'Born dead'] as $key => $label)
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700"><p class="text-sm text-gray-500">{{ $label }}</p><p class="text-2xl font-semibold">{{ number_format($data['totals'][$key]) }}</p></div>
                @endforeach
            </div>
        @endif

        @php($rows = match($tab) { 'reproduction' => $data['rows'], 'health' => $data['byEvent'], default => $data })
        @if(count($rows) === 0)
            <p class="text-sm text-gray-500">No records for this farm and period.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" data-report="{{ $tab }}">
                    <thead class="text-gray-500"><tr class="border-b border-gray-200 dark:border-gray-700">
                        @switch($tab)
                            @case('events')<th class="py-2">Event</th><th>Family</th><th class="text-right">Events</th><th class="text-right">Last recorded</th>@break
                            @case('reproduction')<th class="py-2">Mother</th><th class="text-right">Births</th><th class="text-right">Born alive</th><th class="text-right">Born dead</th><th class="text-right">Last birth</th>@break
                            @case('production')<th class="py-2">Date</th><th>Product</th><th class="text-right">Quantity</th><th class="text-right">Damaged</th><th class="text-right">Animals</th>@break
                            @case('health')<th class="py-2">Event</th><th class="text-right">Events</th><th class="text-right">Animals</th>@break
                            @default<th class="py-2">Species</th><th>Mode</th><th>Status</th><th class="text-right">Records</th><th class="text-right">Head count</th>
                        @endswitch
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($rows as $row)
                            <tr>
                                @switch($tab)
                                    @case('events')<td class="py-2">{{ str($row->event_code)->headline() }}</td><td>{{ $families[$row->family] ?? '—' }}</td><td class="text-right">{{ number_format($row->events) }}</td><td class="text-right">{{ \Carbon\Carbon::parse($row->last_occurred_at)->format('j M Y') }}</td>@break
                                    @case('reproduction')<td class="py-2">{{ $row->display_name ?: 'Unnamed' }}</td><td class="text-right">{{ $row->births_count }}</td><td class="text-right">{{ $row->total_born_alive }}</td><td class="text-right">{{ $row->total_born_dead }}</td><td class="text-right">{{ $row->last_birth_at ? \Carbon\Carbon::parse($row->last_birth_at)->format('j M Y') : '—' }}</td>@break
                                    @case('production')<td class="py-2">{{ \Carbon\Carbon::parse($row->production_date)->format('j M Y') }}</td><td>{{ str($row->product_code)->headline() }}</td><td class="text-right">{{ $fmt($row->quantity) }} {{ $row->unit }}</td><td class="text-right">{{ $fmt($row->damaged_quantity) }}</td><td class="text-right">{{ $row->subjects }}</td>@break
                                    @case('health')<td class="py-2">{{ str($row->event_code)->headline() }}</td><td class="text-right">{{ number_format($row->events) }}</td><td class="text-right">{{ number_format($row->subjects) }}</td>@break
                                    @default<td class="py-2">{{ $row->species_name }}</td><td>{{ $row->unit_type === 'group' ? 'Group' : 'Individual' }}</td><td>{{ str($row->lifecycle_status ?? 'active')->headline() }}</td><td class="text-right">{{ number_format($row->subjects) }}</td><td class="text-right">{{ number_format($row->head_count) }}</td>
                                @endswitch
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
