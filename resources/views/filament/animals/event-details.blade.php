<div class="space-y-4 text-sm">
    <div class="flex flex-wrap items-center gap-2">
        @if($family)<x-filament::badge color="info">{{ $family }}</x-filament::badge>@endif
        <x-filament::badge :color="$record->record_status === 'voided' ? 'danger' : 'success'">{{ str($record->record_status)->headline() }}</x-filament::badge>
        <span class="text-gray-500">{{ $record->occurred_at?->format('j M Y H:i') }}</span>
    </div>
    <dl class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-2">
        <dt class="text-gray-500">Animal / Group</dt><dd class="text-right font-medium">{{ $record->subject?->display_name ?: 'Unnamed' }}</dd>
        @foreach($rows as $row)
            <dt class="text-gray-500">{{ $row['label'] }}</dt><dd class="text-right font-medium">{{ $row['value'] }}</dd>
        @endforeach
        <dt class="text-gray-500">Form version</dt><dd class="text-right text-gray-500">{{ $record->form_version }}</dd>
        <dt class="text-gray-500">Revision</dt><dd class="text-right text-gray-500">{{ $record->revision }}</dd>
    </dl>
    @if($record->record_status === 'voided')
        <p class="text-danger-600">Voided {{ $record->voided_at?->format('j M Y H:i') }}: {{ $record->void_reason }}</p>
    @endif
</div>
