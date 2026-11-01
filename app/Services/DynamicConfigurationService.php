<?php

namespace App\Services;

use App\Models\AnimalBlueprint;
use App\Models\AnimalSpecies;
use App\Models\EventDefinition;
use App\Models\FormDefinition;
use App\Models\LookupSet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DynamicConfigurationService
{
    private const COMPONENTS = ['text', 'multiline_text', 'integer', 'decimal', 'date', 'date_time', 'boolean', 'single_select', 'multi_select', 'measurement', 'photo', 'file', 'identifier_scanner', 'subject_reference', 'location', 'repeatable_group'];

    /** Must match FormSchemaValidator::MOBILE_COMPONENTS in the backend. */
    public const MOBILE_COMPONENTS = ['text', 'multiline_text', 'integer', 'decimal', 'date', 'date_time', 'boolean', 'single_select', 'multi_select', 'measurement', 'identifier_scanner', 'location', 'subject_reference', 'repeatable_group', 'photo'];

    /** Must match FormSchemaValidator::ITEM_COMPONENTS in the backend. */
    public const ITEM_COMPONENTS = ['text', 'multiline_text', 'integer', 'decimal', 'date', 'boolean', 'single_select', 'measurement'];

    public const MEASUREMENT_UNITS = ['kg' => 'Kilogram (kg)', 'g' => 'Gram (g)', 'l' => 'Litre (L)', 'ml' => 'Millilitre (mL)', 'cm' => 'Centimetre (cm)', 'm' => 'Metre (m)', 'celsius' => 'Degrees Celsius (°C)', 'percent' => 'Percent (%)', 'count' => 'Count'];

    private const RESERVED = [
        'id', 'uuid', 'farmUuid', 'farm_uuid', 'speciesUuid', 'species_uuid',
        'blueprintUuid', 'blueprint_uuid', 'blueprintVersion', 'blueprint_version',
        'createdAt', 'created_at', 'updatedAt', 'updated_at', 'deletedAt', 'deleted_at',
        'isDeleted', 'is_deleted', 'revision', 'baseRevision', 'base_revision',
        'createdByUuid', 'created_by_uuid', 'updatedByUuid', 'updated_by_uuid',
        'deletedByUuid', 'deleted_by_uuid', 'restoredByUuid', 'restored_by_uuid',
        'restoredAt', 'restored_at', 'delete_reason', 'syncAction', 'sync_action', 'synced',
        'idempotencyUuid', 'idempotency_uuid', 'sourceDeviceUuid', 'source_device_uuid',
    ];

    public function publishForm(FormDefinition $form, User $actor): FormDefinition
    {
        if ($form->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'Only draft forms can be published.']);
        }
        $schema = $this->normalizeForm($form->schema_json);
        $this->validateForm($schema);
        app(FormLocalization::class)->assertPublishable($schema);
        $form->forceFill([
            'schema_json' => $schema,
            'status' => 'published',
            'checksum' => 'sha256:'.hash('sha256', json_encode($schema, JSON_THROW_ON_ERROR)),
            'published_at' => now(),
        ])->save();

        return $form->fresh();
    }

    public function cloneForm(FormDefinition $form, User $actor, ?string $newCode = null): FormDefinition
    {
        return DB::transaction(function () use ($form, $actor, $newCode): FormDefinition {
            $code = $newCode ?: ($form->code . '_copy');
            $maxVersion = (int) FormDefinition::where('code', $code)->max('version');

            return FormDefinition::create([
                'uuid' => (string) Str::uuid(),
                'code' => $code,
                'version' => $maxVersion + 1,
                'purpose' => $form->purpose,
                'status' => 'draft',
                'schema_json' => $form->schema_json,
                'created_by_uuid' => $actor->uuid,
            ]);
        });
    }

    public function reuseOrCloneEvent(
        AnimalBlueprint $blueprint,
        EventDefinition $sourceEvent,
        bool $cloneForm = false,
        ?string $customName = null,
        ?string $customCode = null,
        ?User $actor = null
    ): EventDefinition {
        return DB::transaction(function () use ($blueprint, $sourceEvent, $cloneForm, $customName, $customCode, $actor) {
            $actor ??= auth()->user() ?? User::first();
            $formUuid = $sourceEvent->form_uuid;
            $formVersion = $sourceEvent->form_version;

            if ($cloneForm) {
                $sourceForm = FormDefinition::where('uuid', $sourceEvent->form_uuid)->firstOrFail();
                $baseCode = $customCode ?: $sourceEvent->code;
                $newFormCode = Str::slug($blueprint->code . '_' . $baseCode, '_');

                $clonedForm = $this->cloneForm($sourceForm, $actor, $newFormCode);
                $clonedForm = $this->publishForm($clonedForm, $actor);

                $formUuid = $clonedForm->uuid;
                $formVersion = $clonedForm->version;
            }

            $code = $customCode ?: $sourceEvent->code;
            $name = $customName ?: $sourceEvent->name;

            // Ensure unique code on this blueprint
            $existing = EventDefinition::where('blueprint_uuid', $blueprint->uuid)->where('code', $code)->first();
            if ($existing) {
                $code = $code . '_' . strtolower(Str::random(4));
            }

            return EventDefinition::create([
                'uuid' => (string) Str::uuid(),
                'blueprint_uuid' => $blueprint->uuid,
                'blueprint_version' => $blueprint->version,
                'code' => $code,
                'name' => $name,
                'translations_json' => array_merge($sourceEvent->translations_json ?? [], ['en' => $name]),
                'form_uuid' => $formUuid,
                'form_version' => $formVersion,
                'behavior_code' => $sourceEvent->behavior_code,
                'family' => $sourceEvent->family,
                'result_rule_json' => $sourceEvent->result_rule_json,
                'eligibility_rule_json' => $sourceEvent->eligibility_rule_json,
                'repeatability_rule_json' => $sourceEvent->repeatability_rule_json,
                'sort_order' => $sourceEvent->sort_order ?? 0,
                'status' => 'draft',
            ]);
        });
    }

    public function publishBlueprint(AnimalBlueprint $blueprint, User $actor): AnimalBlueprint
    {
        if ($blueprint->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'Only draft blueprints can be published.']);
        }
        $form = FormDefinition::query()->where('uuid', $blueprint->registration_form_uuid)->where('version', $blueprint->registration_form_version)->firstOrFail();
        if ($form->status !== 'published') {
            throw ValidationException::withMessages(['registration_form_uuid' => 'Publish the registration form first.']);
        }
        $binding = $blueprint->settings_json['catalogBinding'] ?? null;
        if (is_array($binding) && array_filter($binding) !== []) {
            Validator::make($binding, [
                'livestockTypeId' => ['required', 'integer', 'exists:livestock_types,id'],
                'speciesId' => ['required', 'integer', 'exists:species,id'],
            ])->validate();
            $typeId = DB::table('species')->where('id', $binding['speciesId'])->value('livestockTypeId');
            if ($typeId !== null && (int) $typeId !== (int) $binding['livestockTypeId']) {
                throw ValidationException::withMessages(['settings_json.catalogBinding.speciesId' => 'Species must belong to the selected animal type.']);
            }
        }
        $modes = $blueprint->settings_json['subjectModes'] ?? [];
        if (! is_array($modes) || $modes === []) {
            throw ValidationException::withMessages(['settings_json' => 'At least one subject mode is required.']);
        }
        $policy = $blueprint->settings_json['identificationPolicy'] ?? [];
        $allowedIdentifiers = is_array($policy['allowed'] ?? null) ? $policy['allowed'] : [];
        $requiredIdentifiers = is_array($policy['required'] ?? null) ? $policy['required'] : [];
        if (array_diff($requiredIdentifiers, $allowedIdentifiers) !== []) {
            throw ValidationException::withMessages(['settings_json' => 'Every required identifier must also be allowed.']);
        }
        $knownIdentifiers = DB::table('identifier_types')->where('status', 'active')->pluck('code')->all();
        if (array_diff($allowedIdentifiers, $knownIdentifiers) !== []) {
            throw ValidationException::withMessages(['settings_json' => 'The identification policy contains an unknown or inactive type.']);
        }
        $capabilities = AnimalSpecies::query()->where('uuid', $blueprint->species_uuid)->value('capabilities_json');
        $capabilities = is_string($capabilities) ? json_decode($capabilities, true) : $capabilities;
        foreach (EventDefinition::query()->where('blueprint_uuid', $blueprint->uuid)->where('blueprint_version', $blueprint->version)->get() as $event) {
            $eventForm = FormDefinition::query()->where('uuid', $event->form_uuid)->where('version', $event->form_version)->first();
            if (! $eventForm || $eventForm->status !== 'published') {
                throw ValidationException::withMessages(['events' => "Publish the form for event {$event->code} first."]);
            }
            $errors = $this->eventErrors($event, $eventForm->schema_json, $modes, $capabilities);
            if ($errors !== []) {
                throw ValidationException::withMessages(['events' => "Event {$event->code}: ".collect($errors)->map(fn ($error) => "{$error['path']}: {$error['code']}")->implode(', ')]);
            }
        }

        return DB::transaction(function () use ($blueprint, $actor, $modes): AnimalBlueprint {
            $compiled = ['subjectModes' => $modes, 'registrationForm' => $blueprint->registration_form_uuid.'@'.$blueprint->registration_form_version];
            $blueprint->forceFill([
                'status' => 'published', 'compiled_manifest_json' => $compiled,
                'checksum' => 'sha256:'.hash('sha256', json_encode($compiled, JSON_THROW_ON_ERROR)),
                'published_by_uuid' => $actor->uuid, 'published_at' => now(),
            ])->save();
            AnimalSpecies::query()->where('uuid', $blueprint->species_uuid)->update(['status' => 'published', 'published_at' => now()]);
            EventDefinition::query()->where('blueprint_uuid', $blueprint->uuid)->where('blueprint_version', $blueprint->version)->update(['status' => 'published']);
            $this->requestManifest($actor, 'blueprint.published');

            return $blueprint->fresh();
        });
    }

    public function cloneBlueprint(AnimalBlueprint $blueprint, User $actor): AnimalBlueprint
    {
        return DB::transaction(function () use ($blueprint, $actor): AnimalBlueprint {
            $draft = AnimalBlueprint::create([
                'species_uuid' => $blueprint->species_uuid, 'code' => $blueprint->code,
                'version' => ((int) AnimalBlueprint::where('species_uuid', $blueprint->species_uuid)->max('version')) + 1,
                'status' => 'draft', 'registration_form_uuid' => $blueprint->registration_form_uuid,
                'registration_form_version' => $blueprint->registration_form_version,
                'settings_json' => $blueprint->settings_json, 'created_by_uuid' => $actor->uuid,
            ]);
            foreach (EventDefinition::where('blueprint_uuid', $blueprint->uuid)->where('blueprint_version', $blueprint->version)->where('status', '!=', 'retired')->get() as $event) {
                $copy = $event->replicate(['uuid']);
                $copy->forceFill(['uuid' => (string) Str::uuid(), 'blueprint_uuid' => $draft->uuid, 'blueprint_version' => $draft->version, 'status' => 'draft'])->save();
            }

            return $draft;
        });
    }

    public function publishLookup(LookupSet $lookup, User $actor): LookupSet
    {
        if ($lookup->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'Only draft lookup sets can be published.']);
        }
        if (! $lookup->options()->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['options' => 'At least one active option is required.']);
        }
        if ($lookup->parent_lookup_set_uuid === $lookup->uuid) {
            throw ValidationException::withMessages(['parent_lookup_set_uuid' => 'A lookup cannot be its own parent.']);
        }
        $active = $lookup->options()->where('status', 'active')->get();
        if ($lookup->parent_lookup_set_uuid) {
            $validParents = DB::table('lookup_options')->where('lookup_set_uuid', $lookup->parent_lookup_set_uuid)->pluck('uuid');
            if ($active->contains(fn ($option) => ! $option->parent_option_uuid || ! $validParents->contains($option->parent_option_uuid))) {
                throw ValidationException::withMessages(['options' => 'Every active option must select an option from the configured parent lookup set.']);
            }
        } elseif ($active->contains(fn ($option) => $option->parent_option_uuid !== null)) {
            throw ValidationException::withMessages(['options' => 'Remove parent options or configure a parent lookup set.']);
        }
        $lookup->forceFill(['status' => 'published', 'published_at' => now()])->save();
        $this->requestManifest($actor, 'lookup_set.published');

        return $lookup->fresh();
    }

    private function validateForm(array $schema): void
    {
        $keys = [];
        $errors = [];
        foreach ($schema['sections'] ?? [] as $sectionIndex => $section) {
            foreach ($section['fields'] ?? [] as $fieldIndex => $field) {
                $path = "sections.$sectionIndex.fields.$fieldIndex";
                $key = $field['key'] ?? null;
                if (isset($field['role']) && (! is_string($field['role']) || ! preg_match('/^[a-z][a-z0-9_]*$/', $field['role']))) {
                    $errors[] = ['code' => 'FIELD_ROLE_INVALID', 'path' => "$path.role"];
                }
                if (($field['component'] ?? null) === 'repeatable_group') {
                    array_push($errors, ...$this->itemFieldErrors($field['item_fields'] ?? null, "$path.item_fields"));
                }
                if (($field['component'] ?? null) === 'subject_reference') {
                    $reference = $field['reference'] ?? [];
                    if (! is_array($reference) || array_diff(array_keys($reference), ['same_species', 'sex_codes', 'multiple', 'unit_types']) !== []) {
                        $errors[] = ['code' => 'SUBJECT_REFERENCE_INVALID', 'path' => "$path.reference"];
                    }
                }
                if (isset($field['core_binding']) && ! in_array($field['core_binding'], ['name', 'breedName', 'gender', 'sexCode', 'identificationNumber', 'dateOfBirth', 'dateFirstEnteredToFarm', 'weightAsOnRegistration', 'stageId', 'motherUuid', 'fatherUuid', 'primaryColor', 'secondaryColor', 'isIdentified', 'newWeight', 'oldWeight', 'vaccineUuid', 'diseaseId', 'remarks', 'eventDate', 'quantity'], true)) {
                    $errors[] = ['code' => 'FIELD_CORE_BINDING_UNSUPPORTED', 'path' => "$path.core_binding"];
                }
                if (! is_string($key) || ! preg_match('/^[a-z][a-z0-9_]*$/', $key) || in_array($key, self::RESERVED, true) || isset($keys[$key])) {
                    $errors[] = ['code' => 'FIELD_KEY_INVALID_RESERVED_OR_DUPLICATE', 'path' => "$path.key"];
                }
                $keys[$key] = true;
                if (! in_array($field['component'] ?? null, self::COMPONENTS, true)) {
                    $errors[] = ['code' => 'FIELD_COMPONENT_UNSUPPORTED', 'path' => "$path.component"];
                } elseif (! in_array($field['component'], self::MOBILE_COMPONENTS, true)) {
                    $errors[] = ['code' => 'FIELD_COMPONENT_NOT_AVAILABLE_ON_MOBILE', 'path' => "$path.component"];
                }
                if (($field['component'] ?? null) === 'measurement' && ! array_key_exists($field['unit'] ?? '', self::MEASUREMENT_UNITS)) {
                    $errors[] = ['code' => 'MEASUREMENT_UNIT_REQUIRED', 'path' => "$path.unit"];
                }
                if (! in_array($field['value_type'] ?? null, ['string', 'integer', 'decimal', 'boolean', 'date', 'date_time', 'array', 'object', 'uuid', 'measurement', 'file'], true)) {
                    $errors[] = ['code' => 'FIELD_VALUE_TYPE_UNSUPPORTED', 'path' => "$path.value_type"];
                }
                if (! in_array($field['data_owner'] ?? 'user_input', ['user_input', 'server_generated', 'derived'], true)) {
                    $errors[] = ['code' => 'FIELD_DATA_OWNER_INVALID', 'path' => "$path.data_owner"];
                }
                if (($field['required'] ?? 'optional') === 'always' && ($field['nullable'] ?? true)) {
                    $errors[] = ['code' => 'REQUIRED_FIELD_NULLABLE', 'path' => $path];
                }
                if (($field['data_owner'] ?? 'user_input') !== 'user_input') {
                    $errors[] = ['code' => 'FIELD_DERIVATION_NOT_IMPLEMENTED', 'path' => "$path.data_owner"];
                }
                if (in_array($field['component'] ?? null, ['single_select', 'multi_select'], true)) {
                    $inline = $field['options'] ?? [];
                    $lookup = $field['lookup_code'] ?? null;
                    if ((! empty($inline) && ! empty($lookup)) || (empty($inline) && empty($lookup))) {
                        $errors[] = ['code' => 'ONE_CHOICE_SOURCE_REQUIRED', 'path' => "$path.options"];
                    }
                    if ($lookup && ! LookupSet::where('code', $lookup)->where('status', 'published')->exists()) {
                        $errors[] = ['code' => 'LOOKUP_NOT_PUBLISHED', 'path' => "$path.lookup_code"];
                    }
                    if (isset($field['lookup_parent_field']) && (! is_string($field['lookup_parent_field']) || $field['lookup_parent_field'] === $key || ! isset($keys[$field['lookup_parent_field']]))) {
                        $errors[] = ['code' => 'LOOKUP_PARENT_FIELD_INVALID_OR_NOT_EARLIER', 'path' => "$path.lookup_parent_field"];
                    }
                    $values = array_column($inline, 'value');
                    if (count($values) !== count(array_unique($values, SORT_REGULAR))) {
                        $errors[] = ['code' => 'DUPLICATE_CHOICE_VALUE', 'path' => "$path.options"];
                    }
                }
                $validation = $field['validation'] ?? [];
                if (isset($validation['min'], $validation['max']) && $validation['min'] > $validation['max']) {
                    $errors[] = ['code' => 'VALIDATION_MIN_GREATER_THAN_MAX', 'path' => "$path.validation"];
                }
                if (isset($validation['min_length'], $validation['max_length']) && $validation['min_length'] > $validation['max_length']) {
                    $errors[] = ['code' => 'VALIDATION_MIN_LENGTH_GREATER_THAN_MAX', 'path' => "$path.validation"];
                }
            }
        }
        if ($keys === []) {
            $errors[] = ['code' => 'BUSINESS_FIELD_REQUIRED', 'path' => 'sections'];
        }
        if ($errors !== []) {
            throw ValidationException::withMessages(['data.schema_json.sections' => array_map(fn ($error) => $error['path'].': '.$error['code'], $errors)]);
        }
    }

    private function normalizeForm(array $schema): array
    {
        foreach ($schema['sections'] ?? [] as $sectionIndex => $section) {
            foreach ($section['fields'] ?? [] as $fieldIndex => $field) {
                $validation = is_array($field['validation'] ?? null) ? $field['validation'] : [];
                foreach (['min', 'max', 'min_length', 'max_length'] as $key) {
                    if (isset($validation[$key]) && is_numeric($validation[$key])) {
                        $validation[$key] = $validation[$key] + 0;
                    }
                }
                if (in_array($field['component'] ?? null, ['single_select', 'multi_select'], true) && ! empty($field['options'])) {
                    $validation['allowed_values'] = array_values(array_map(fn (array $option) => $option['value'], $field['options']));
                }
                $schema['sections'][$sectionIndex]['fields'][$fieldIndex]['validation'] = $validation;
            }
        }

        return $schema;
    }

    /**
     * Queued in the caller's transaction; the backend compiles the manifest (its only builder)
     * before it next serves one, so devices never see a manifest without the change.
     */
    public function requestManifest(User $actor, string $reason): void
    {
        DB::table('configuration_publish_requests')->insert([
            'requested_by_uuid' => $actor->uuid, 'reason' => $reason, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /**
     * Behaviour contract check for one event; also used by the event builder before saving.
     *
     * @return array<int, array{code: string, path: string}>
     */
    public function eventErrors(EventDefinition $event, array $schema, array $modes, ?array $capabilities): array
    {
        return app(BehaviorCatalog::class)->validateEvent($event->behavior_code, $schema, $modes, $capabilities);
    }

    private function itemFieldErrors(mixed $itemFields, string $path): array
    {
        if (! is_array($itemFields) || $itemFields === [] || ! array_is_list($itemFields)) {
            return [['code' => 'REPEATABLE_ITEM_FIELDS_REQUIRED', 'path' => $path]];
        }
        $errors = [];
        $keys = [];
        foreach ($itemFields as $index => $item) {
            $key = is_array($item) ? ($item['key'] ?? null) : null;
            if (! is_string($key) || ! preg_match('/^[a-z][a-z0-9_]*$/', $key) || isset($keys[$key])) {
                $errors[] = ['code' => 'FIELD_KEY_INVALID', 'path' => "$path.$index.key"];
            }
            $keys[(string) $key] = true;
            if (! in_array($item['component'] ?? null, self::ITEM_COMPONENTS, true)) {
                $errors[] = ['code' => 'REPEATABLE_ITEM_COMPONENT_UNSUPPORTED', 'path' => "$path.$index.component"];
            }
            if (($item['component'] ?? null) === 'measurement' && ! array_key_exists($item['unit'] ?? '', self::MEASUREMENT_UNITS)) {
                $errors[] = ['code' => 'MEASUREMENT_UNIT_REQUIRED', 'path' => "$path.$index.unit"];
            }
            if (! filled($item['label'] ?? null)) {
                $errors[] = ['code' => 'FIELD_LABEL_REQUIRED', 'path' => "$path.$index.label"];
            }
        }

        return $errors;
    }
}
