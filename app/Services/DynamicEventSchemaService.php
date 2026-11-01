<?php

namespace App\Services;

use App\Models\EventDefinition;
use App\Models\EventRecord;
use App\Models\FormDefinition;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DynamicEventSchemaService
{
    /** @var array<string, array<string, array{key: string, label: string, component: string, unit: ?string, sort: int}>> */
    private static array $cachedFields = [];

    /**
     * Get all event types configured in the platform (from EventDefinition and EventRecord).
     *
     * @return Collection<string, array{code: string, name: string, count: int, definition: ?EventDefinition}>
     */
    public function getEventTypes(): Collection
    {
        $definitions = EventDefinition::query()
            ->select('code', 'name', 'uuid', 'form_uuid', 'behavior_code', 'family')
            ->distinct()
            ->orderBy('name')
            ->get()
            ->keyBy('code');

        $recordedCodes = EventRecord::query()
            ->distinct()
            ->whereNotNull('event_code')
            ->pluck('event_code');

        $allCodes = $definitions->keys()->merge($recordedCodes)->unique()->filter(fn ($c) => $c !== 'legacy_history')->sort();

        return $allCodes->mapWithKeys(function (string $code) use ($definitions) {
            $def = $definitions->get($code);
            $name = $def?->name ?? Str::headline(str_replace('record_', '', $code));

            return [
                $code => [
                    'code' => $code,
                    'name' => $name,
                    'definition' => $def,
                ],
            ];
        });
    }

    /**
     * Get primary fields for a given event code from its FormDefinition or payload samples.
     *
     * @return array<string, array{key: string, label: string, component: string, unit: ?string, sort: int}>
     */
    public function getFieldsForEvent(string $eventCode): array
    {
        if (isset(self::$cachedFields[$eventCode])) {
            return self::$cachedFields[$eventCode];
        }

        $fields = [];
        $ignoredKeys = [
            'id', 'uuid', 'subject_uuid', 'farm_uuid', 'event_code', 'idempotency_uuid',
            'created_at', 'updated_at', 'deleted_at', 'legacy_id', 'legacy_data', 'notes', 'remarks',
            'sync_action', 'synced',
        ];

        // 1. Try finding via EventDefinition & FormDefinition
        $definitions = EventDefinition::where('code', $eventCode)->get();
        $formUuids = $definitions->pluck('form_uuid')->filter()->unique();

        if ($formUuids->isNotEmpty()) {
            $forms = FormDefinition::whereIn('uuid', $formUuids)->get();
            $sort = 0;
            foreach ($forms as $form) {
                foreach ($form->schema_json['sections'] ?? [] as $section) {
                    foreach ($section['fields'] ?? [] as $field) {
                        $key = $field['key'] ?? null;
                        if (! $key || in_array($key, $ignoredKeys, true) || isset($fields[$key])) {
                            continue;
                        }

                        $label = $field['label'] ?? null;
                        if (is_array($label)) {
                            $label = $label['en'] ?? reset($label);
                        }
                        if (empty($label)) {
                            $label = Str::headline($key);
                        }

                        $fields[$key] = [
                            'key' => $key,
                            'label' => (string) $label,
                            'component' => $field['component'] ?? 'text',
                            'unit' => $field['unit'] ?? null,
                            'sort' => $sort++,
                        ];
                    }
                }
            }
        }

        // 2. Fallback / supplement with real stored payload keys
        if (count($fields) < 6) {
            $samplePayloads = EventRecord::query()
                ->where('event_code', $eventCode)
                ->latest('id')
                ->limit(15)
                ->pluck('payload_json');

            $sort = count($fields);
            foreach ($samplePayloads as $payload) {
                if (! is_array($payload)) {
                    continue;
                }
                foreach ($payload as $key => $val) {
                    if (in_array($key, $ignoredKeys, true) || isset($fields[$key])) {
                        continue;
                    }
                    $fields[$key] = [
                        'key' => (string) $key,
                        'label' => Str::headline((string) $key),
                        'component' => is_numeric($val) ? 'number' : (is_bool($val) ? 'boolean' : 'text'),
                        'unit' => null,
                        'sort' => $sort++,
                    ];
                }
            }
        }

        // Prioritize up to top 6 most informative columns per event
        uasort($fields, fn ($a, $b) => $a['sort'] <=> $b['sort']);
        $fields = array_slice($fields, 0, 6, true);

        return self::$cachedFields[$eventCode] = $fields;
    }
}
