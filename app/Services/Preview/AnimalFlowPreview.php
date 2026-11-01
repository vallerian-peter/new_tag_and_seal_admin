<?php

namespace App\Services\Preview;

use App\Models\AnimalBlueprint;
use App\Models\EventDefinition;
use App\Models\FormDefinition;
use App\Models\LookupSet;
use App\Services\BehaviorMapping;

/** Read-only presentation of exact definition versions; creates no animal data. */
class AnimalFlowPreview
{
    /** Must match the unit symbols shown by the Flutter measurement input. */
    private const UNIT_SYMBOLS = ['kg' => 'kg', 'g' => 'g', 'l' => 'L', 'ml' => 'mL', 'cm' => 'cm', 'm' => 'm', 'celsius' => '°C', 'percent' => '%', 'count' => ''];

    public function __construct(private readonly RuleEvaluator $rules) {}

    public function text(mixed $value, string $locale, string $fallback = ''): string
    {
        if (is_string($value)) {
            return trim($value) !== '' ? $value : $fallback;
        }
        $base = strtolower(explode('-', str_replace('_', '-', $locale))[0]);
        foreach ([$locale, $base, 'en'] as $key) {
            if (is_array($value) && is_string($value[$key] ?? null) && trim($value[$key]) !== '') {
                return $value[$key];
            }
        }

        return $fallback;
    }

    public function blueprint(AnimalBlueprint $blueprint, array $facts, string $locale, array $values): array
    {
        $registration = FormDefinition::where('uuid', $blueprint->registration_form_uuid)->where('version', $blueprint->registration_form_version)->first();
        $modes = $blueprint->settings_json['subjectModes'] ?? [];
        $modeAllowed = in_array($facts['subject']['unit_type'], $modes, true);
        $events = EventDefinition::where('blueprint_uuid', $blueprint->uuid)->where('blueprint_version', $blueprint->version)->orderBy('sort_order')->get();

        return [
            'name' => $this->text($blueprint->species?->translations_json, $locale, $blueprint->species?->name ?? $blueprint->code),
            'code' => $blueprint->code, 'version' => $blueprint->version,
            'modeAllowed' => $modeAllowed,
            'binding' => $blueprint->settings_json['catalogBinding'] ?? null,
            'registration' => $registration ? $this->form($registration, $facts, $locale, $values) : null,
            'events' => $events->map(function ($event) use ($facts, $locale, $values, $modeAllowed): array {
                $form = FormDefinition::where('uuid', $event->form_uuid)->where('version', $event->form_version)->first();

                $legacyType = $event->result_rule_json['legacyLogType'] ?? match ($event->code) {
                    'record_weight' => 'weightChange', 'record_vaccination' => 'vaccination', default => null,
                };

                return [
                    'uuid' => $event->uuid, 'name' => $this->text($event->translations_json, $locale, $event->name),
                    'eligible' => $modeAllowed && $event->status !== 'retired' && $this->rules->evaluate($event->eligibility_rule_json, $facts),
                    'status' => $event->status, 'behavior' => $event->behavior_code,
                    'explanation' => $this->explain($event->behavior_code),
                    'legacyLogType' => $event->result_rule_json['legacyLogType'] ?? match ($event->code) {
                        'record_weight' => 'weightChange', 'record_vaccination' => 'vaccination', default => null,
                    },
                    'form' => $form ? $this->form($form, $facts, $locale, $values, $legacyType, app(BehaviorMapping::class)->badges($form, $event->behavior_code)) : null,
                ];
            })->all(),
        ];
    }

    /** @param  array<string, string>  $roles  field key => behaviour input label */
    public function form(FormDefinition $form, array $facts, string $locale, array $values, ?string $legacyType = null, array $roles = []): array
    {
        $schema = $form->schema_json;
        $key = 'f_'.substr(hash('sha256', $form->uuid.'@'.$form->version), 0, 20);
        $facts['values'] = $values[$key] ?? [];
        $sections = [];
        foreach ($schema['sections'] ?? [] as $section) {
            $fields = [];
            foreach ($section['fields'] ?? [] as $field) {
                if (! $this->rules->evaluate($field['visibility_rule'] ?? null, $facts)) {
                    continue;
                }
                $options = $field['options'] ?? [];
                if ($options === [] && ! empty($field['lookup_code'])) {
                    $lookup = LookupSet::where('code', $field['lookup_code'])->where('status', 'published')->orderByDesc('version')->first();
                    $options = $lookup?->options()->where('status', 'active')->orderBy('sort_order')->get()->map(fn ($option) => [
                        'value' => $option->code, 'label' => array_replace(['en' => $option->label], $option->translations_json ?? []),
                    ])->all() ?? [];
                }
                $fields[] = [
                    'key' => $field['key'], 'component' => $field['component'],
                    'unit' => self::UNIT_SYMBOLS[$field['unit'] ?? ''] ?? null,
                    'label' => $this->text($field['presentation']['label'] ?? $field['label'] ?? null, $locale),
                    'help' => $this->text($field['presentation']['helpText'] ?? $field['help_text'] ?? null, $locale),
                    'placeholder' => $this->text($field['presentation']['placeholder'] ?? $field['placeholder'] ?? null, $locale),
                    'required' => ($field['required'] ?? '') === 'always',
                    'role' => $roles[$field['key']] ?? (filled($field['role'] ?? null) ? str($field['role'])->headline()->toString() : null),
                    'reference' => $field['reference'] ?? [],
                    'items' => array_map(fn (array $item) => [
                        'label' => $this->text($item['label'] ?? null, $locale, $item['key'] ?? ''),
                        'component' => $item['component'] ?? 'text',
                        'unit' => self::UNIT_SYMBOLS[$item['unit'] ?? ''] ?? null,
                        'required' => ($item['required'] ?? '') === 'always',
                    ], is_array($field['item_fields'] ?? null) ? $field['item_fields'] : []),
                    'binding' => $field['core_binding'] ?? ($form->purpose === 'registration' ? match ($field['key']) {
                        'display_name' => 'name', 'breed_name' => 'breedName', default => null,
                    } : match ([$legacyType, $field['key']]) {
                        ['weightChange', 'weight'] => 'newWeight', ['vaccination', 'vaccine'] => 'vaccineUuid',
                        default => $legacyType && $field['key'] === 'remarks' ? 'remarks' : null,
                    }), 'validation' => $field['validation'] ?? [],
                    'options' => array_map(fn ($option) => ['value' => $option['value'], 'label' => $this->text($option['label'] ?? null, $locale)], $options),
                ];
            }
            $sections[] = ['label' => $this->text($section['presentation']['title'] ?? $section['label'] ?? null, $locale), 'fields' => $fields];
        }

        return ['key' => $key, 'code' => $form->code, 'version' => $form->version,
            'title' => $this->text($schema['presentation']['title'] ?? null, $locale, $form->code),
            'description' => $this->text($schema['presentation']['description'] ?? null, $locale),
            'submitLabel' => $this->text($schema['presentation']['submitLabel'] ?? null, $locale, 'Save'),
            'sections' => $sections];
    }

    private function explain(string $behavior): string
    {
        return match ($behavior) {
            'record_only' => 'Stores the submitted event and its versioned field values.',
            'update_weight' => 'Stores the event and updates the animal’s current weight.',
            'transition_stage' => 'Stores the event and changes the configured lifecycle stage.',
            'change_lifecycle_status' => 'Stores the event and changes the animal’s business status.',
            'record_egg_or_clutch' => 'Stores egg or clutch counts for the animal or group.',
            'record_hatching' => 'Records hatching and its resulting offspring or groups.',
            'record_spawning' => 'Records spawning using the configured reproduction data.',
            'create_offspring' => 'Records reproduction and creates the configured offspring.',
            'record_birth' => 'Counts born alive and born dead, registers listed offspring with the mother, and updates reproduction totals.',
            'record_mortality' => 'Records deaths in a group and reduces its quantity.',
            'record_treatment' => 'Records the product and dose given to the animal or group.',
            'split_group', 'merge_groups', 'adjust_group_quantity', 'record_group_census' => 'Records the event and applies the configured group quantity operation.',
            'record_production_yield', 'record_biomass_sample', 'record_environment_measurement' => 'Records the configured production or measurement values and updates its projection.',
            'transfer_subject' => 'Records movement and validates access to the destination farm.',
            'assign_identifier', 'retire_identifier' => 'Applies the configured identification policy and records the change.',
            'schedule_followup' => 'Records the event and its configured follow-up.',
            default => 'This behavior requires a supported backend handler before it can be used.',
        };
    }
}
