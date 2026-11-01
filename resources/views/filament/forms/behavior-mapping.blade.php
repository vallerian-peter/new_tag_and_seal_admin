@if($mapping['behavior'])
    <div class="space-y-3" data-behavior-mapping>
        <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $mapping['behavior']['name'] }}
            · {{ $mapping['behavior']['reversible'] ? 'Can be voided and reversed' : 'Cannot be reversed once saved' }}
        </p>
        @if($mapping['rows'] === [])
            <p class="text-sm text-gray-500">This behaviour reads no form inputs; it only stores the event.</p>
        @else
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <tr><th class="px-3 py-2">Behaviour input</th><th class="px-3 py-2">Input types</th><th class="px-3 py-2">Form field</th><th class="px-3 py-2">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($mapping['rows'] as $row)
                            <tr>
                                <td class="px-3 py-2 font-medium">{{ $row['label'] }}@if($row['required']) <span class="text-xs text-gray-500">(required)</span>@endif</td>
                                <td class="px-3 py-2 text-gray-500">{{ implode(', ', $row['components']) }}</td>
                                <td class="px-3 py-2">{{ $row['field'] ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    @if($row['problems'] !== [])
                                        <x-filament::badge color="danger">Needs attention</x-filament::badge>
                                    @elseif($row['field'])
                                        <x-filament::badge color="success">Mapped</x-filament::badge>
                                    @else
                                        <x-filament::badge color="gray">Not used</x-filament::badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @if($mapping['errors'] !== [])
            <ul class="list-disc space-y-1 ps-5 text-sm text-danger-600 dark:text-danger-400" role="alert">
                @foreach($mapping['errors'] as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        @endif
    </div>
@endif
