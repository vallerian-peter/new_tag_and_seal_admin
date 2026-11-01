<?php

namespace App\Services;

use App\Models\LookupSet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Lookup sets are the only place reference lists are edited. Sets copied from a
 * legacy reference table (`legacy_table`) are written back to that table so app
 * versions that still read it see the same list, until the table is retired.
 */
class LegacyReferenceSync
{
    /** Required legacy columns that have no lookup equivalent. */
    private const DEFAULTS = ['semen_straw_types' => ['category' => 'AI']];

    public function __construct(private readonly DynamicConfigurationService $configuration) {}

    public function push(LookupSet $set, User $actor): void
    {
        $table = $set->legacy_table;
        if (! $table || ! Schema::hasTable($table)) {
            return;
        }
        DB::transaction(function () use ($set, $table, $actor): void {
            $speciesTypeId = $this->speciesTypeId($set, $table);
            $hasSwahili = Schema::hasColumn($table, 'name_sw');
            $used = $set->options()->pluck('code')->all();
            foreach ($set->options()->orderBy('sort_order')->get() as $option) {
                $values = ['name' => $option->label, 'updated_at' => now()];
                if ($hasSwahili) {
                    $values['name_sw'] = $option->translations_json['sw'] ?? null;
                }
                if ($option->legacy_id === null) {
                    $id = DB::table($table)->insertGetId($values + ($speciesTypeId ? ['livestockTypeId' => $speciesTypeId] : []) + (self::DEFAULTS[$table] ?? []) + ['created_at' => now()]);
                    $option->forceFill(['legacy_id' => $id, 'code' => $option->code ?: $this->code($option->label, $used)])->save();
                } else {
                    DB::table($table)->where('id', $option->legacy_id)->update($values);
                }
                // A legacy row without a species appears in every species' set; keep their labels equal.
                DB::table('lookup_options')->join('lookup_sets', 'lookup_sets.uuid', '=', 'lookup_options.lookup_set_uuid')
                    ->where('lookup_sets.legacy_table', $table)->where('lookup_options.legacy_id', $option->legacy_id)
                    ->where('lookup_options.uuid', '!=', $option->uuid)
                    ->update(['lookup_options.label' => $option->label, 'lookup_options.translations_json' => json_encode($option->translations_json), 'lookup_options.updated_at' => now()]);
            }
            $this->configuration->requestManifest($actor, 'reference_list.saved');
        });
    }

    /** Sets of species-specific tables are named legacy.{table}.{species}. */
    private function speciesTypeId(LookupSet $set, string $table): ?int
    {
        if (! Schema::hasColumn($table, 'livestockTypeId') || ! str_starts_with($set->code, "legacy.$table.")) {
            return null;
        }
        $species = substr($set->code, strlen("legacy.$table."));

        return ($id = DB::table('animal_species')->where('code', $species)->value('legacy_livestock_type_id')) ? (int) $id : null;
    }

    private function code(string $label, array &$used): string
    {
        $code = Str::of($label)->ascii()->lower()->slug('_')->toString() ?: 'option';
        $candidate = $code;
        for ($i = 2; in_array($candidate, $used, true); $i++) {
            $candidate = "{$code}_$i";
        }
        $used[] = $candidate;

        return $candidate;
    }
}
