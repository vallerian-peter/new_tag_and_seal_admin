<?php

namespace Tests\Feature;

use App\Filament\Resources\AnimalBlueprints\Pages\CreateAnimalBlueprint;
use App\Filament\Resources\AnimalBlueprints\Pages\EditAnimalBlueprint;
use App\Filament\Resources\AnimalBlueprints\Pages\ListAnimalBlueprints;
use App\Filament\Resources\AnimalBlueprints\Pages\PreviewAnimalBlueprint;
use App\Filament\Resources\AnimalSubjects\Pages\ListAnimalSubjects;
use App\Filament\Resources\EventDefinitions\Pages\CreateEventDefinition;
use App\Filament\Resources\EventDefinitions\Pages\ListEventDefinitions;
use App\Filament\Resources\FormDefinitions\Pages\CreateFormDefinition;
use App\Filament\Resources\FormDefinitions\Pages\EditFormDefinition;
use App\Filament\Resources\FormDefinitions\Pages\ListFormDefinitions;
use App\Filament\Resources\FormDefinitions\Pages\PreviewFormDefinition;
use App\Filament\Resources\PlatformUpgradeRuns\Pages\ListPlatformUpgradeRuns;
use App\Models\AnimalBlueprint;
use App\Models\AnimalSpecies;
use App\Models\AnimalSubject;
use App\Models\EventDefinition;
use App\Models\FormDefinition;
use App\Models\LookupSet;
use App\Models\PlatformUpgradeRun;
use App\Models\User;
use App\Services\DynamicConfigurationService;
use App\Services\EligibilityEditor;
use App\Services\Preview\RuleEvaluator;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class DynamicPortalTest extends TestCase
{
    private \Closure $undoRepeaterFake;

    protected function setUp(): void
    {
        parent::setUp();
        // Never migrate or truncate the shared application database in UI tests.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        // Use the real backend-owned schema, allowing a separate API process to
        // consume a snapshot of exactly what the Portal published.
        foreach (glob(base_path('../new-tag-and-seal-laravel-backend/database/migrations/*.php')) as $migration) {
            (require $migration)->up();
        }
        $user = User::create(['username' => 'Portal tester', 'email' => 'portal@example.test', 'password' => 'test-password', 'role' => 'systemUser', 'roleId' => 0, 'status' => 'active']);
        foreach (DB::table('platform_permissions')->pluck('id') as $id) {
            DB::table('user_platform_permissions')->insert(['user_uuid' => $user->uuid, 'permission_id' => $id]);
        }
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->undoRepeaterFake = Repeater::fake();
    }

    protected function tearDown(): void
    {
        ($this->undoRepeaterFake)();
        DB::purge('sqlite');
        parent::tearDown();
    }

    public function test_dynamic_pages_render_real_filament_components(): void
    {
        foreach ([ListFormDefinitions::class, CreateFormDefinition::class, ListAnimalBlueprints::class, CreateAnimalBlueprint::class, ListEventDefinitions::class, CreateEventDefinition::class, ListAnimalSubjects::class, ListPlatformUpgradeRuns::class] as $page) {
            Livewire::test($page)->assertSuccessful();
        }
        $run = PlatformUpgradeRun::create(['uuid' => (string) Str::uuid(), 'release' => 'dynamic-test-release', 'environment' => 'testing', 'state' => 'verified', 'target_schema_version' => '1', 'started_at' => now()]);
        Livewire::test(ListPlatformUpgradeRuns::class)->assertCanSeeTableRecords([$run])->assertSee('dynamic-test-release')->assertSee('verified');
    }

    public function test_form_creation_and_publish_save_visible_edits_and_protect_metadata(): void
    {
        Livewire::test(CreateFormDefinition::class)->fillForm([
            'code' => 'test.registration', 'version' => 1, 'purpose' => 'registration',
            'schema_json' => $this->formSchema(), 'status' => 'published', 'system_envelope_version' => 999,
        ])->call('create')->assertHasNoFormErrors();
        $form = FormDefinition::sole();
        $this->assertSame('draft', $form->status);
        $this->assertEquals(1, $form->system_envelope_version);
        Livewire::test(EditFormDefinition::class, ['record' => $form->getRouteKey()])
            ->fillForm(['code' => 'test.registration.saved'])->callAction('publish')->assertHasNoFormErrors();
        $this->assertSame('test.registration.saved', $form->fresh()->code);
        $this->assertSame('published', $form->fresh()->status);
        Livewire::test(ListFormDefinitions::class)->callTableAction('view', $form)->assertSuccessful();
        Livewire::test(EditFormDefinition::class, ['record' => $form->getRouteKey()])->assertForbidden();
    }

    public function test_portal_publishes_versioned_forms_and_events_into_a_mobile_manifest(): void
    {
        $regions = LookupSet::create(['code' => 'geo.regions', 'name' => 'Regions', 'version' => 1]);
        $dar = $regions->options()->create(['code' => 'dar_es_salaam', 'label' => 'Dar es Salaam', 'translations_json' => ['en' => 'Dar es Salaam', 'sw' => 'Dar es Salaam']]);
        $arusha = $regions->options()->create(['code' => 'arusha', 'label' => 'Arusha', 'translations_json' => ['en' => 'Arusha', 'sw' => 'Arusha']]);
        app(DynamicConfigurationService::class)->publishLookup($regions->fresh(), auth()->user());
        $districts = LookupSet::create(['code' => 'geo.districts', 'name' => 'Districts', 'version' => 1, 'parent_lookup_set_uuid' => $regions->uuid]);
        $districts->options()->create(['code' => 'kinondoni', 'label' => 'Kinondoni', 'parent_option_uuid' => $dar->uuid]);
        $districts->options()->create(['code' => 'meru', 'label' => 'Meru', 'parent_option_uuid' => $arusha->uuid]);
        app(DynamicConfigurationService::class)->publishLookup($districts->fresh(), auth()->user());
        $species = AnimalSpecies::create(['code' => 'rabbit', 'name' => 'Rabbit']);
        $registration = $this->publishedForm('rabbit.registration', 'registration', 3, expandedMatrix: true);
        $eventForm = $this->publishedForm('rabbit.observation', 'event', 2, expandedMatrix: true);
        Livewire::test(CreateAnimalBlueprint::class)->fillForm([
            'species_uuid' => $species->uuid, 'code' => 'rabbit.standard', 'version' => 2,
            'registration_form_uuid' => $registration->uuid, 'registration_form_version' => 99,
            'settings_json' => ['subjectModes' => ['individual', 'group'], 'allowedSexCodes' => ['female', 'male', 'unknown'], 'reproductionMode' => 'live_birth', 'locales' => ['en', 'sw'], 'identificationPolicy' => ['allowed' => [], 'required' => [], 'allowWithoutTag' => true]],
        ])->call('create')->assertHasNoFormErrors();
        $blueprint = AnimalBlueprint::sole();
        $this->assertEquals(3, $blueprint->registration_form_version);
        $eventData = ['blueprint_uuid' => $blueprint->uuid, 'blueprint_version' => 99, 'code' => 'observation', 'name' => 'Observation', 'translations_json' => ['en' => 'Observation', 'sw' => 'Uchunguzi'], 'form_uuid' => $eventForm->uuid, 'form_version' => 99, 'behavior_code' => 'record_only'];
        Livewire::test(CreateEventDefinition::class)->fillForm($eventData + ['eligibility_rule_json' => '{broken'])
            ->call('create')->assertHasFormErrors(['eligibility_rule_json' => 'json']);
        Livewire::test(CreateEventDefinition::class)->fillForm($eventData)->call('create')->assertHasNoFormErrors();
        $event = EventDefinition::sole();
        $this->assertEquals(2, $event->blueprint_version);
        $this->assertEquals(2, $event->form_version);
        Livewire::test(EditAnimalBlueprint::class, ['record' => $blueprint->getRouteKey()])->callAction('publish')->assertHasNoFormErrors();
        Livewire::test(ListAnimalBlueprints::class)->callTableAction('view', $blueprint)->assertSuccessful();
        Livewire::test(ListEventDefinitions::class)->callTableAction('view', $event)->assertSuccessful();
        $this->assertSame($registration->uuid.'@3', $blueprint->fresh()->compiled_manifest_json['registrationForm']);
        $this->assertSame('published', $event->fresh()->status);
        // The backend compiles the manifest from this request (see PortalSharedDatabaseTest).
        $this->assertDatabaseHas('configuration_publish_requests', [
            'reason' => 'blueprint.published',
            'fulfilled_at' => null,
        ]);
        if ($database = getenv('DYNAMIC_PORTAL_DATABASE_FIXTURE')) {
            $directory = realpath(dirname($database));
            if (! $directory || ! str_starts_with($directory, realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR)) {
                throw new \LogicException('Shared test database must be in a temporary directory.');
            }
            DB::connection()->getPdo()->exec('VACUUM INTO '.DB::connection()->getPdo()->quote($database));
        }
    }

    public function test_blueprint_clone_keeps_its_event_forms_and_rules_as_independent_drafts(): void
    {
        $this->test_portal_publishes_versioned_forms_and_events_into_a_mobile_manifest();
        $source = AnimalBlueprint::sole();
        $original = EventDefinition::sole();
        $draft = app(DynamicConfigurationService::class)->cloneBlueprint($source, auth()->user());
        $copied = EventDefinition::where('blueprint_uuid', $draft->uuid)->sole();
        $this->assertSame($source->code, $draft->code);
        $this->assertSame('draft', $copied->status);
        $this->assertNotSame($original->uuid, $copied->uuid);
        $this->assertSame($original->form_uuid, $copied->form_uuid);
        $this->assertSame($original->form_version, $copied->form_version);
        $this->assertEquals($original->eligibility_rule_json, $copied->eligibility_rule_json);
        $this->assertSame('published', $original->fresh()->status);
    }

    public function test_reserved_fields_cannot_be_published(): void
    {
        $form = FormDefinition::create(['code' => 'invalid.registration', 'version' => 1, 'purpose' => 'registration', 'schema_json' => $this->formSchema('deleted_by_uuid')]);
        Livewire::test(EditFormDefinition::class, ['record' => $form->getRouteKey()])->callAction('publish')->assertHasErrors(['data.schema_json.sections']);
        $this->assertSame('draft', $form->fresh()->status);
    }

    public function test_publication_blocks_inputs_the_mobile_app_cannot_render_and_requires_measurement_units(): void
    {
        $schema = $this->formSchema();
        $field = $schema['sections'][0]['fields'][0];
        $schema['sections'][0]['fields'] = array_slice($schema['sections'][0]['fields'], 0, 2);
        $schema['sections'][0]['fields'][] = ['field_uuid' => (string) Str::uuid(), 'key' => 'certificate', 'component' => 'file', 'value_type' => 'file'] + $field;
        $schema['sections'][0]['fields'][] = ['field_uuid' => (string) Str::uuid(), 'key' => 'girth', 'component' => 'measurement', 'value_type' => 'decimal'] + $field;
        $schema['sections'][0]['fields'][] = ['field_uuid' => (string) Str::uuid(), 'key' => 'piglets', 'component' => 'repeatable_group', 'value_type' => 'array'] + $field;
        $form = FormDefinition::create(['code' => 'unsupported.inputs', 'version' => 1, 'purpose' => 'event', 'schema_json' => $schema]);
        try {
            app(DynamicConfigurationService::class)->publishForm($form->fresh(), auth()->user());
            $this->fail('Publication accepted inputs the mobile app cannot render.');
        } catch (ValidationException $exception) {
            $messages = implode(' ', $exception->errors()['data.schema_json.sections']);
            $this->assertStringContainsString('sections.0.fields.2.component: FIELD_COMPONENT_NOT_AVAILABLE_ON_MOBILE', $messages);
            $this->assertStringContainsString('sections.0.fields.3.unit: MEASUREMENT_UNIT_REQUIRED', $messages);
            $this->assertStringContainsString('sections.0.fields.4.item_fields: REPEATABLE_ITEM_FIELDS_REQUIRED', $messages);
        }
        $this->assertSame('draft', $form->fresh()->status);

        $schema['sections'][0]['fields'] = [$schema['sections'][0]['fields'][0], ['unit' => 'kg'] + $schema['sections'][0]['fields'][3]];
        $form->update(['schema_json' => $schema]);
        app(DynamicConfigurationService::class)->publishForm($form->fresh(), auth()->user());
        $page = Livewire::test(PreviewFormDefinition::class, ['record' => $form->getRouteKey()])->assertSuccessful();
        $this->assertStringContainsString('>kg</span>', $page->html());
    }

    public function test_configuration_pages_require_permission(): void
    {
        DB::table('user_platform_permissions')->delete();
        Livewire::test(ListFormDefinitions::class)->assertForbidden();
        Livewire::test(CreateEventDefinition::class)->assertForbidden();
    }

    public function test_localization_editor_requires_default_labels_and_shows_translation_progress(): void
    {
        $schema = $this->formSchema();
        $schema['sections'][0]['fields'][0]['label'] = ['en' => '', 'sw' => 'Maelezo'];
        Livewire::test(CreateFormDefinition::class)->fillForm([
            'code' => 'missing.translation', 'version' => 1, 'purpose' => 'registration', 'schema_json' => $schema,
        ])->call('create')->assertHasFormErrors(['schema_json.sections.0.fields.0.label.en' => 'required']);
        $form = $this->publishedForm('localized.registration', 'registration', 1);
        Livewire::test(ListFormDefinitions::class)->assertSee('Kiswahili')->assertCanSeeTableRecords([$form]);
    }

    public function test_publication_rejects_missing_english_even_when_the_editor_is_bypassed(): void
    {
        $schema = $this->formSchema();
        $schema['sections'][0]['fields'][0]['label'] = ['sw' => 'Maelezo'];
        $form = FormDefinition::create(['code' => 'invalid.locale', 'version' => 1, 'purpose' => 'registration', 'schema_json' => $schema]);
        try {
            app(DynamicConfigurationService::class)->publishForm($form->fresh(), auth()->user());
            $this->fail('Publication accepted a missing English label.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('data.schema_json.sections.0.fields.0.label.en', $exception->errors());
        }
        $this->assertSame('draft', $form->fresh()->status);
    }

    public function test_lookup_publication_queues_the_manifest_for_the_backend(): void
    {
        $lookup = LookupSet::create(['code' => 'check_kind', 'name' => 'Check kind', 'version' => 1]);
        $lookup->options()->create(['code' => 'routine', 'label' => 'Routine check', 'translations_json' => ['en' => '', 'sw' => 'Ukaguzi wa kawaida']]);
        app(DynamicConfigurationService::class)->publishLookup($lookup->fresh(), auth()->user());
        $this->assertSame('published', $lookup->fresh()->status);
        $this->assertDatabaseHas('configuration_publish_requests', ['reason' => 'lookup_set.published', 'requested_by_uuid' => auth()->user()->uuid, 'fulfilled_at' => null]);
        $this->assertDatabaseCount('configuration_revisions', 0);
    }

    public function test_archive_and_restore_actions_preserve_history_and_restrict_trash_visibility(): void
    {
        $subject = AnimalSubject::create([
            'uuid' => (string) Str::uuid(), 'farm_uuid' => (string) Str::uuid(), 'species_uuid' => (string) Str::uuid(),
            'blueprint_uuid' => (string) Str::uuid(), 'blueprint_version' => 1, 'unit_type' => 'individual',
            'display_name' => 'Archive test', 'attributes_json' => [],
        ]);
        Livewire::test(ListAnimalSubjects::class)->callTableAction('archive', $subject, data: ['reason' => 'Duplicate record'])
            ->assertHasNoTableActionErrors()->assertCanNotSeeTableRecords([$subject]);
        $this->assertTrue($subject->fresh()->trashed());
        $this->assertDatabaseHas('sync_tombstones', ['entity_uuid' => $subject->uuid, 'entity_revision' => 2]);
        $permission = DB::table('platform_permissions')->where('code', 'records.restore')->value('id');
        DB::table('user_platform_permissions')->where('permission_id', $permission)->delete();
        Livewire::test(ListAnimalSubjects::class)->set('tableFilters', ['trashed' => ['value' => false]])->assertCanNotSeeTableRecords([$subject]);
        DB::table('user_platform_permissions')->insert(['user_uuid' => auth()->user()->uuid, 'permission_id' => $permission]);
        Livewire::test(ListAnimalSubjects::class)->filterTable('trashed', false)->assertCanSeeTableRecords([$subject])
            ->callTableAction('restore', $subject)->assertHasNoTableActionErrors();
        $this->assertFalse($subject->fresh()->trashed());
        $this->assertSame(3, $subject->fresh()->revision);
        $this->assertDatabaseCount('audit_logs', 2);
    }

    public function test_animal_preview_resolves_exact_forms_and_filters_events_by_context_without_writes(): void
    {
        $species = AnimalSpecies::create(['code' => 'preview_animal', 'name' => 'Preview animal', 'translations_json' => ['sw' => 'Mnyama wa hakiki']]);
        $registration = $this->publishedForm('preview.registration', 'registration', 1);
        $eventForm = $this->publishedForm('preview.event', 'event', 2);
        $blueprint = AnimalBlueprint::create(['species_uuid' => $species->uuid, 'code' => 'preview.standard', 'version' => 1,
            'registration_form_uuid' => $registration->uuid, 'settings_json' => ['subjectModes' => ['individual', 'group']], 'status' => 'draft']);
        $event = EventDefinition::create(['blueprint_uuid' => $blueprint->uuid, 'code' => 'female_observation', 'name' => 'Female observation',
            'translations_json' => ['sw' => 'Uchunguzi wa jike'], 'form_uuid' => $eventForm->uuid, 'behavior_code' => 'record_only',
            'eligibility_rule_json' => ['all' => [['fact' => 'subject.sex_code', 'operator' => 'equals', 'value' => 'female'], ['fact' => 'subject.unit_type', 'operator' => 'equals', 'value' => 'individual']]],
            'status' => 'draft']);
        $page = Livewire::test(PreviewAnimalBlueprint::class, ['record' => $blueprint->getRouteKey()])
            ->assertSuccessful()->assertSee('Preview animal')->assertSee('Female observation')->assertSee('preview.event')
            ->assertSee('Kind of observation')->assertSee('Routine check');
        $page->set('sexCode', 'male')->assertSee('Not eligible for the selected animal context.')->assertDontSee('preview.event');
        $page->set('sexCode', 'female')->set('previewLocale', 'sw')->assertSee('Uchunguzi wa jike')->assertSee('Uchunguzi wa mnyama')->assertSee('Ukaguzi wa kawaida');
        $page->set('unitType', 'group')->assertSet('sexCode', 'not_applicable')->assertSee('Not eligible for the selected animal context.');
        $this->assertDatabaseCount('animal_subjects', 0);
        $this->assertDatabaseCount('event_records', 0);
        $this->assertSame(2, $event->fresh()->form_version);
    }

    public function test_form_preview_is_read_only_and_shows_draft_wording_and_lookup_choices(): void
    {
        $form = $this->publishedForm('preview.standalone', 'event', 1);
        $before = $form->schema_json;
        $page = Livewire::test(PreviewFormDefinition::class, ['record' => $form->getRouteKey()])
            ->assertSuccessful()->assertSee('Animal observation')->assertSee('Describe what you observed.')->assertSee('Routine check');
        $key = 'f_'.substr(hash('sha256', $form->uuid.'@'.$form->version), 0, 20);
        $page->set('previewValues.'.$key.'.notes', 'Preview input')->set('previewLocale', 'sw')
            ->assertSee('Rekodi uchunguzi wako.')->assertSee('Hifadhi uchunguzi');
        $this->assertSame($before, $form->fresh()->schema_json);
        $this->assertDatabaseCount('event_records', 0);
    }

    public function test_preview_requires_configuration_view_permission(): void
    {
        $form = $this->publishedForm('preview.private', 'event', 1);
        DB::table('user_platform_permissions')->delete();
        Livewire::test(PreviewFormDefinition::class, ['record' => $form->getRouteKey()])->assertForbidden();
    }

    public function test_guided_applicability_edits_the_rule_used_by_preview(): void
    {
        $rule = EligibilityEditor::update(null, 'subject.sex_code', ['female']);
        $rule = EligibilityEditor::update($rule, 'subject.unit_type', ['individual']);
        $this->assertSame(['female'], EligibilityEditor::selected($rule, 'subject.sex_code'));
        $evaluator = app(RuleEvaluator::class);
        $this->assertTrue($evaluator->evaluate($rule, ['subject' => ['sex_code' => 'female', 'unit_type' => 'individual']]));
        $this->assertFalse($evaluator->evaluate($rule, ['subject' => ['sex_code' => 'male', 'unit_type' => 'individual']]));
        $this->assertFalse($evaluator->evaluate($rule, ['subject' => ['sex_code' => 'female', 'unit_type' => 'group']]));
        $rule = EligibilityEditor::update($rule, 'subject.sex_code', []);
        $this->assertTrue($evaluator->evaluate($rule, ['subject' => ['sex_code' => 'male', 'unit_type' => 'individual']]));
    }

    private function publishedForm(string $code, string $purpose, int $version, bool $expandedMatrix = false): FormDefinition
    {
        Livewire::test(CreateFormDefinition::class)->fillForm([
            'code' => $code, 'version' => $version, 'purpose' => $purpose, 'schema_json' => $this->formSchema(expandedMatrix: $expandedMatrix),
        ])->call('create')->assertHasNoFormErrors();
        $form = FormDefinition::where('code', $code)->sole();
        Livewire::test(EditFormDefinition::class, ['record' => $form->getRouteKey()])->callAction('publish')->assertHasNoFormErrors();

        return $form->fresh();
    }

    private function formSchema(string $key = 'notes', bool $expandedMatrix = false): array
    {
        $schema = ['presentation' => [
            'title' => ['en' => 'Animal observation', 'sw' => 'Uchunguzi wa mnyama'],
            'description' => ['en' => 'Record your observation.', 'sw' => 'Rekodi uchunguzi wako.'],
            'submitLabel' => ['en' => 'Save observation', 'sw' => 'Hifadhi uchunguzi'],
        ], 'sections' => [['key' => 'details', 'label' => ['en' => 'Details', 'sw' => 'Maelezo'], 'fields' => [[
            'field_uuid' => (string) Str::uuid(), 'key' => $key, 'label' => ['en' => 'Notes', 'sw' => 'Maelezo'],
            'help_text' => ['en' => 'Describe what you observed.', 'sw' => 'Eleza ulichoona.'],
            'placeholder' => ['en' => 'Enter notes', 'sw' => 'Weka maelezo'],
            'validation_messages' => ['REQUIRED' => ['en' => 'Observation notes are required.', 'sw' => 'Maelezo ya uchunguzi yanahitajika.']],
            'component' => 'text', 'value_type' => 'string', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 0,
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'observation_kind', 'label' => ['en' => 'Kind of observation', 'sw' => 'Aina ya uchunguzi'],
            'component' => 'single_select', 'value_type' => 'string', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 1,
            'options' => [['value' => 'routine', 'label' => ['en' => 'Routine check', 'sw' => 'Ukaguzi wa kawaida']]],
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'body_weight', 'label' => ['en' => 'Body weight', 'sw' => 'Uzito wa mwili'],
            'component' => 'measurement', 'unit' => 'kg', 'value_type' => 'decimal', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 2,
            'validation' => ['min' => 0, 'max' => 50],
            'validation_messages' => ['MAXIMUM' => ['en' => 'Weight cannot exceed 50 kg.', 'sw' => 'Uzito hauwezi kuzidi kilo 50.']],
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'vaccinated', 'label' => ['en' => 'Vaccinated', 'sw' => 'Amechanjwa'],
            'component' => 'boolean', 'value_type' => 'boolean', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 3,
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'signs', 'label' => ['en' => 'Signs seen', 'sw' => 'Dalili zilizoonekana'],
            'component' => 'multi_select', 'value_type' => 'array', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 4,
            'options' => [
                ['value' => 'cough', 'label' => ['en' => 'Cough', 'sw' => 'Kikohozi']],
                ['value' => 'fever', 'label' => ['en' => 'Fever', 'sw' => 'Homa']],
            ],
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'checked_on', 'label' => ['en' => 'Checked on', 'sw' => 'Tarehe ya ukaguzi'],
            'component' => 'date', 'value_type' => 'date', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 5,
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'pasture', 'label' => ['en' => 'Pasture location', 'sw' => 'Eneo la malisho'],
            'component' => 'location', 'value_type' => 'object', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 6,
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'respiratory_rate', 'label' => ['en' => 'Respiratory rate', 'sw' => 'Kasi ya kupumua'],
            'component' => 'integer', 'value_type' => 'integer', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 7,
            'validation' => ['min' => 1, 'max' => 200],
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'examined_at', 'label' => ['en' => 'Examined at', 'sw' => 'Muda wa ukaguzi'],
            'component' => 'date_time', 'value_type' => 'date_time', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 8,
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'region', 'label' => ['en' => 'Region', 'sw' => 'Mkoa'],
            'component' => 'single_select', 'value_type' => 'string', 'data_owner' => 'user_input', 'required' => 'always', 'nullable' => false, 'sort_order' => 9,
            'lookup_code' => 'geo.regions',
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'district', 'label' => ['en' => 'District', 'sw' => 'Wilaya'],
            'component' => 'single_select', 'value_type' => 'string', 'data_owner' => 'user_input', 'required' => 'always', 'nullable' => false, 'sort_order' => 10,
            'lookup_code' => 'geo.districts', 'lookup_parent_field' => 'region',
            'validation_messages' => ['LOOKUP_PARENT_MISMATCH' => ['en' => 'Select a district in the chosen region.', 'sw' => 'Chagua wilaya iliyo ndani ya mkoa uliochagua.']],
        ], [
            'field_uuid' => (string) Str::uuid(), 'key' => 'treatments', 'label' => ['en' => 'Treatments', 'sw' => 'Matibabu'],
            'component' => 'repeatable_group', 'value_type' => 'array', 'data_owner' => 'user_input', 'required' => 'optional', 'nullable' => true, 'sort_order' => 11,
            'validation' => ['max_items' => 3],
            'item_fields' => [[
                'key' => 'medicine', 'label' => ['en' => 'Medicine', 'sw' => 'Dawa'], 'component' => 'text', 'required' => 'always',
            ], [
                'key' => 'dose', 'label' => ['en' => 'Dose', 'sw' => 'Dozi'], 'component' => 'measurement', 'unit' => 'ml', 'required' => 'always',
            ]],
        ]]]]];

        if (! $expandedMatrix) {
            $schema['sections'][0]['fields'] = array_slice($schema['sections'][0]['fields'], 0, 7);
        }

        return $schema;
    }
}
