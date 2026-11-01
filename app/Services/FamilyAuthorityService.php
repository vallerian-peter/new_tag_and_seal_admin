<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

/**
 * Per-family switch between the legacy log tables and the event ledger.
 * Moves one step at a time (legacy ⇄ ledger_shadow ⇄ ledger) so every family is
 * compared in shadow mode before the app reads it from the ledger.
 */
class FamilyAuthorityService
{
    public const STATES = [
        'legacy' => 'Legacy tables', 'ledger_shadow' => 'Shadow (both, legacy shown)', 'ledger' => 'Event ledger',
        'retired' => 'Retired (ledger only, legacy read-only)',
    ];

    private const ORDER = ['legacy', 'ledger_shadow', 'ledger', 'retired'];

    public const APP_VERSION_FLAG = 'app.minimum_version';

    public const DEFAULT_APP_VERSION = '1.1.2';

    public const ARCHIVE_COMMAND = 'php artisan tag-and-seal:ledger archive';

    /** Legacy log tables each family replaces; matches the backend LegacyCatalog. */
    public const LEGACY_TABLES = [
        'births' => ['birth_events', 'aborted_pregnancies'],
        'health' => ['vaccinations', 'dewormings', 'treatments', 'prepuce_conditions'],
        'production' => ['milkings'],
        'breeding' => ['inseminations', 'pregnancies', 'dryoffs'],
        'growth' => ['weight_changes', 'feedings', 'iron_injections', 'teeth_clippings', 'tail_dockings', 'livestock_markings', 'stage_changes'],
        'movement' => ['transfers', 'disposals'],
    ];

    public const RECONCILE_COMMAND = 'php artisan tag-and-seal:ledger reconcile';

    public function __construct(private readonly DynamicConfigurationService $configuration) {}

    /** @return array<int, array{family: string, authority: string, legacy: int, upgraded: int, ledger: int, changedAt: ?string, reconciliation: ?array}> */
    public function overview(): array
    {
        $flags = DB::table('platform_feature_flags')->where('key', 'like', 'family.%.authority')->get()->keyBy('key');

        return collect(self::LEGACY_TABLES)->map(function (array $tables, string $family) use ($flags) {
            $flag = $flags->get("family.$family.authority");
            $existing = array_values(array_filter($tables, fn ($table) => Schema::hasTable($table)));

            return [
                'family' => $family,
                'authority' => json_decode((string) $flag?->value_json, true)['authority'] ?? 'legacy',
                'legacy' => collect($existing)->sum(fn ($table) => DB::table($table)->count()),
                'upgraded' => DB::table('legacy_record_mappings')->whereIn('source_table', $existing)->where('target_type', 'event_record')->count(),
                'ledger' => DB::table('event_records as e')
                    ->leftJoin('event_definitions as d', 'd.uuid', '=', 'e.event_definition_uuid')
                    ->leftJoin('behavior_definitions as b', 'b.code', '=', 'e.behavior_code')
                    ->where(DB::raw('coalesce(d.family, b.family)'), $family)->where('e.record_status', '!=', 'voided')->count(),
                'changedAt' => $flag?->updated_at,
                'reconciliation' => $this->latestReconciliation($family),
            ];
        })->values()->all();
    }

    /** @return array{run: string, passed: bool, failed: array<int, array{code: string, expected: int, actual: int}>, at: ?string}|null */
    public function latestReconciliation(string $family): ?array
    {
        $run = DB::table('reconciliation_results')->where('entity_family', $family)->orderByDesc('id')->value('run_uuid');
        if (! $run) {
            return null;
        }
        $checks = DB::table('reconciliation_results')->where('run_uuid', $run)->get();

        return [
            'run' => $run,
            'passed' => $checks->every(fn ($check) => (bool) $check->passed),
            'failed' => $checks->reject(fn ($check) => (bool) $check->passed)->map(fn ($check) => [
                'code' => $check->check_code, 'expected' => (int) $check->expected_count, 'actual' => (int) $check->actual_count,
            ])->values()->all(),
            'at' => $checks->max('created_at'),
        ];
    }

    /** @return array<int, string> states one step away from the current one */
    public function nextStates(string $current): array
    {
        $index = array_search($current, self::ORDER, true);

        return array_values(array_filter([self::ORDER[$index - 1] ?? null, self::ORDER[$index + 1] ?? null]));
    }

    public function ledgerBlocker(string $family): ?string
    {
        $reconciliation = $this->latestReconciliation($family);

        return $reconciliation && $reconciliation['passed'] ? null
            : 'The latest reconciliation for this family must pass before the app reads it from the ledger. Run: '.self::RECONCILE_COMMAND." $family";
    }

    /** Retiring stops legacy writes, so app versions that still write legacy rows must already be refused at login. */
    public function retireBlocker(): ?string
    {
        return $this->appVersion()['enforced'] ? null
            : 'Enforce a minimum app version first, so app versions that still write the legacy tables can no longer sign in.';
    }

    /** @return array{version: string, enforced: bool, changedAt: ?string} */
    public function appVersion(): array
    {
        $flag = DB::table('platform_feature_flags')->where('key', self::APP_VERSION_FLAG)->first();

        return [
            'version' => (string) (json_decode((string) $flag?->value_json, true)['version'] ?? self::DEFAULT_APP_VERSION),
            'enforced' => (bool) ($flag?->enabled ?? false),
            'changedAt' => $flag?->updated_at,
        ];
    }

    public function setAppVersion(string $version, bool $enforced, User $actor, string $reason): void
    {
        if (! preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            throw ValidationException::withMessages(['version' => 'Use a version like 1.2.0.']);
        }
        DB::transaction(function () use ($version, $enforced, $actor, $reason): void {
            $before = $this->appVersion();
            $flag = DB::table('platform_feature_flags')->where('key', self::APP_VERSION_FLAG)->lockForUpdate()->first();
            DB::table('platform_feature_flags')->updateOrInsert(['key' => self::APP_VERSION_FLAG], [
                'enabled' => $enforced, 'value_json' => json_encode(['version' => $version]), 'updated_by_uuid' => $actor->uuid,
                'updated_at' => now(), 'created_at' => $flag?->created_at ?? now(),
            ]);
            $this->audit($actor, 'app_version.changed', self::APP_VERSION_FLAG, ['version' => $before['version'], 'enforced' => $before['enforced']], ['version' => $version, 'enforced' => $enforced], $reason);
            $this->configuration->requestManifest($actor, 'app_version.changed');
        });
    }

    public function change(string $family, string $target, User $actor, string $reason): void
    {
        if (! array_key_exists($family, self::LEGACY_TABLES) || ! array_key_exists($target, self::STATES)) {
            throw ValidationException::withMessages(['authority' => 'Unknown family or authority.']);
        }
        DB::transaction(function () use ($family, $target, $actor, $reason): void {
            $key = "family.$family.authority";
            $flag = DB::table('platform_feature_flags')->where('key', $key)->lockForUpdate()->first();
            $current = json_decode((string) $flag?->value_json, true)['authority'] ?? 'legacy';
            if (! in_array($target, $this->nextStates($current), true)) {
                throw ValidationException::withMessages(['authority' => 'Change one step at a time: legacy, shadow, ledger, then retired.']);
            }
            if (in_array($target, ['ledger', 'retired'], true) && $current !== 'retired' && $blocker = $this->ledgerBlocker($family)) {
                throw ValidationException::withMessages(['authority' => $blocker]);
            }
            if ($target === 'retired' && $blocker = $this->retireBlocker()) {
                throw ValidationException::withMessages(['authority' => $blocker]);
            }
            DB::table('platform_feature_flags')->updateOrInsert(['key' => $key], [
                'enabled' => true,
                'value_json' => json_encode(['authority' => $target]),
                'updated_by_uuid' => $actor->uuid,
                'updated_at' => now(),
                'created_at' => $flag?->created_at ?? now(),
            ]);
            $this->audit($actor, 'family_authority.changed', $key, ['family' => $family, 'authority' => $current], ['family' => $family, 'authority' => $target], $reason);
            $this->configuration->requestManifest($actor, 'family_authority.changed');
        });
    }

    private function audit(User $actor, string $action, string $flagKey, array $before, array $after, string $reason): void
    {
        AuditLog::create([
            'uuid' => (string) Str::uuid(),
            'actor_uuid' => $actor->uuid,
            'action' => $action,
            'entity_type' => 'platform_feature_flag',
            'entity_uuid' => Uuid::uuid5(Uuid::NAMESPACE_URL, 'tag-and-seal:'.$flagKey)->toString(),
            'before_json' => $before,
            'after_json' => $after,
            'reason' => $reason,
            'request_uuid' => (string) Str::uuid(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
