<?php

namespace App\Services;

use App\Models\AnimalBlueprint;
use App\Models\AnimalSpecies;
use App\Models\FormDefinition;

/** Shows how an event form feeds its behaviour, and explains publication errors in plain words. */
class BehaviorMapping
{
    public const FAMILIES = [
        'general' => 'General', 'health' => 'Health', 'growth' => 'Growth', 'births' => 'Births',
        'production' => 'Production', 'breeding' => 'Breeding', 'population' => 'Population',
        'lifecycle' => 'Lifecycle', 'movement' => 'Movement', 'identity' => 'Identification',
    ];

    public function __construct(private readonly BehaviorCatalog $catalog) {}

    /** @return array<string, array<string, string>> family label => [behaviour code => name] */
    public function behaviorOptions(): array
    {
        return $this->catalog->all()
            ->groupBy(fn ($behavior) => self::FAMILIES[$behavior->family] ?? str($behavior->family)->headline()->toString())
            ->map(fn ($group) => $group->mapWithKeys(fn ($behavior) => [$behavior->code => $behavior->translations_json['en'] ?? $behavior->code])->all())
            ->all();
    }

    /**
     * @return array{behavior: ?array, rows: array<int, array>, errors: array<int, string>}
     */
    public function analyze(?string $blueprintUuid, ?string $formUuid, ?string $behaviorCode, int|string|null $formVersion = null): array
    {
        $behavior = $this->catalog->find($behaviorCode);
        $form = $formUuid ? FormDefinition::where('uuid', $formUuid)->when(filled($formVersion), fn ($query) => $query->where('version', $formVersion))->orderByDesc('version')->first() : null;
        if (! $behavior || ! $form) {
            return ['behavior' => null, 'rows' => [], 'errors' => []];
        }
        $schema = $form->schema_json ?? [];
        $blueprint = $blueprintUuid ? AnimalBlueprint::where('uuid', $blueprintUuid)->orderByDesc('version')->first() : null;
        $modes = $blueprint?->settings_json['subjectModes'] ?? $behavior->subject_modes_json;
        $capabilities = $blueprint ? AnimalSpecies::where('uuid', $blueprint->species_uuid)->first()?->capabilities_json : null;
        $mapped = $this->catalog->fieldsByRole($behavior, $schema);
        $errors = $this->catalog->validateEvent($behavior->code, $schema, $modes, $capabilities);
        $rows = collect($behavior->roles_json)->map(fn (array $role) => [
            'role' => $role['role'],
            'label' => $role['label']['en'] ?? $role['role'],
            'required' => $role['required'],
            'components' => $role['components'],
            'field' => $mapped[$role['role']]['key'] ?? null,
            'problems' => collect($errors)->where('path', 'roles.'.$role['role'])->pluck('code')->all(),
        ])->all();

        return [
            'behavior' => ['code' => $behavior->code, 'name' => $behavior->translations_json['en'] ?? $behavior->code, 'family' => $behavior->family, 'reversible' => $behavior->reversible],
            'rows' => $rows,
            'errors' => array_map(fn (array $error) => $this->message($error, $behavior->roles_json), $errors),
        ];
    }

    /** @return array<string, string> field key => role label, for preview badges */
    public function badges(FormDefinition $form, ?string $behaviorCode): array
    {
        $behavior = $this->catalog->find($behaviorCode);
        if (! $behavior) {
            return [];
        }
        $labels = collect($behavior->roles_json)->mapWithKeys(fn ($role) => [$role['role'] => $role['label']['en'] ?? $role['role']]);

        return collect($this->catalog->fieldsByRole($behavior, $form->schema_json ?? []))
            ->mapWithKeys(fn (array $field, string $role) => [$field['key'] => $labels[$role] ?? $role])->all();
    }

    public function message(array $error, array $roles): string
    {
        $code = str($error['path'])->after('.')->toString();
        $label = collect($roles)->firstWhere('role', $code)['label']['en'] ?? $code;

        return match ($error['code']) {
            'BEHAVIOR_UNKNOWN' => 'Choose a behaviour from the catalogue.',
            'BEHAVIOR_SUBJECT_MODE_UNSUPPORTED' => 'This behaviour does not support the blueprint’s management modes.',
            'BEHAVIOR_CAPABILITY_MISSING' => "The species is missing the required capability: $code.",
            'BEHAVIOR_ROLE_MISSING' => "Add a form field for “{$label}”.",
            'BEHAVIOR_ROLE_COMPONENT_MISMATCH' => "The field for “{$label}” uses an input type this behaviour cannot read.",
            'BEHAVIOR_ROLE_MUST_BE_REQUIRED' => "The field for “{$label}” must be required.",
            'BEHAVIOR_ROLE_UNKNOWN' => "A field declares “{$code}”, which this behaviour does not read.",
            'BEHAVIOR_ROLE_DUPLICATE' => "Only one field may declare “{$label}”.",
            default => $error['code'],
        };
    }
}
