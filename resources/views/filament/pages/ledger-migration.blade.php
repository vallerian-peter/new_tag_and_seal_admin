<x-filament-panels::page>
    <x-filament::section description="Each event family moves on its own: legacy tables first, then shadow mode where both are written and compared, then the event ledger. Every change publishes a new configuration revision and is audited.">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" data-ledger-migration>
                <thead class="text-gray-500">
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2">Family</th><th>Authority</th>
                        <th class="text-right">Legacy records</th><th class="text-right">Upgraded</th><th class="text-right">Ledger events</th>
                        <th>Reconciliation</th><th class="text-right">Last change</th><th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($families as $family)
                        <tr wire:key="family-{{ $family['family'] }}">
                            <td class="py-3 font-medium">{{ str($family['family'])->headline() }}</td>
                            <td><x-filament::badge :color="match($family['authority']) { 'ledger' => 'success', 'retired' => 'info', 'ledger_shadow' => 'warning', default => 'gray' }" class="inline-flex">{{ $states[$family['authority']] ?? $family['authority'] }}</x-filament::badge></td>
                            <td class="text-right">{{ number_format($family['legacy']) }}</td>
                            <td class="text-right">{{ number_format($family['upgraded']) }}@if($family['legacy'] > 0) <span class="text-gray-500">({{ (int) floor(min($family['upgraded'], $family['legacy']) * 100 / $family['legacy']) }}%)</span>@endif</td>
                            <td class="text-right">{{ number_format($family['ledger']) }}</td>
                            <td>
                                @if($family['reconciliation'])
                                    <x-filament::badge :color="$family['reconciliation']['passed'] ? 'success' : 'danger'" class="inline-flex">{{ $family['reconciliation']['passed'] ? 'Passed' : count($family['reconciliation']['failed']).' failed' }}</x-filament::badge>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($family['reconciliation']['at'])->format('j M Y H:i') }}</span>
                                    @foreach($family['reconciliation']['failed'] as $check)
                                        <div class="text-xs text-danger-600">{{ $check['code'] }}: expected {{ $check['expected'] }}, found {{ $check['actual'] }}</div>
                                    @endforeach
                                @else
                                    <span class="text-gray-500">Not run</span>
                                @endif
                            </td>
                            <td class="text-right text-gray-500">{{ $family['changedAt'] ? \Carbon\Carbon::parse($family['changedAt'])->format('j M Y H:i') : '—' }}</td>
                            <td class="text-right">{{ ($this->changeAuthorityAction)(['family' => $family['family'], 'current' => $family['authority']]) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-sm text-gray-500">Moving a family to the event ledger needs a passing reconciliation. Run <code>{{ \App\Services\FamilyAuthorityService::RECONCILE_COMMAND }} &lt;family&gt;</code> on the API server (add <code>--repair</code> to re-copy legacy rows first). Retiring a family also needs an enforced minimum app version; archive its tables afterwards with <code>{{ \App\Services\FamilyAuthorityService::ARCHIVE_COMMAND }} &lt;family&gt;</code>. Tables are never dropped from here.</p>
    </x-filament::section>
    <x-filament::section heading="Minimum app version" description="Apps older than this are asked to update and cannot sign in while it is enforced.">
        <div class="flex items-center gap-4 text-sm" data-app-version>
            <span class="font-medium">{{ $appVersion['version'] }}</span>
            <x-filament::badge :color="$appVersion['enforced'] ? 'success' : 'gray'" class="inline-flex">{{ $appVersion['enforced'] ? 'Enforced' : 'Not enforced' }}</x-filament::badge>
            @if($appVersion['changedAt'])<span class="text-gray-500">Changed {{ \Carbon\Carbon::parse($appVersion['changedAt'])->format('j M Y H:i') }}</span>@endif
            <span class="ms-auto">{{ $this->appVersionAction }}</span>
        </div>
        @unless($this->canRun())
            <p class="mt-4 text-sm text-gray-500">You can view migration status. Changing authority needs the “Run platform upgrades” permission.</p>
        @endunless
    </x-filament::section>
</x-filament-panels::page>
