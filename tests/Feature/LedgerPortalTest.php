<?php

namespace Tests\Feature;

use App\Filament\Pages\AnimalReports;
use App\Filament\Pages\LedgerMigration;
use App\Filament\Resources\AnimalBlueprints\Pages\PreviewAnimalBlueprint;
use App\Filament\Resources\AnimalSpecies\Pages\CreateAnimalSpecies;
use App\Filament\Resources\AnimalSubjects\Pages\ListAnimalSubjects;
use App\Filament\Resources\AnimalSubjects\Pages\ViewAnimalSubject;
use App\Filament\Resources\EventDefinitions\Pages\CreateEventDefinition;
use App\Filament\Resources\EventRecords\Pages\ListEventRecords;
use App\Filament\Resources\LookupSets\LookupSetResource;
use App\Filament\Resources\LookupSets\Pages\EditLookupSet;
use App\Models\AnimalBlueprint;
use App\Models\AnimalSpecies;
use App\Models\AnimalSubject;
use App\Models\EventDefinition;
use App\Models\EventRecord;
use App\Models\FormDefinition;
use App\Models\LookupSet;
use App\Models\User;
use App\Services\DynamicConfigurationService;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

/** Phase 2: species capabilities, behaviour mapping, animals, events explorer, reports and family authority. */
class LedgerPortalTest extends TestCase
{
    private \Closure $undoRepeaterFake;

    protected function setUp(): void
    {
        parent::setUp();
        // Never migrate or truncate the shared application database in UI tests.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (glob(base_path('../new-tag-and-seal-laravel-backend/database/migrations/*.php')) as $migration) {
            (require $migration)->up();
        }
        $user = User::create(['username' => 'Ledger tester', 'email' => 'ledger@example.test', 'password' => 'test-password', 'role' => 'systemUser', 'roleId' => 0, 'status' => 'active']);
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

    public function test_species_capabilities_and_legacy_type_are_saved_and_queued_for_the_manifest(): void
    {
        $typeId = DB::table('livestock_types')->insertGetId(['name' => 'Rabbitry', 'created_at' => now(), 'updated_at' => now()]);
        Livewire::test(CreateAnimalSpecies::class)->fillForm([
            'code' => 'test_rabbit', 'name' => 'Test rabbit', 'default_subject_mode' => 'individual',
            'legacy_livestock_type_id' => $typeId,
            'capabilities_json' => ['reproduction' => 'live_birth', 'subjectModes' => ['individual', 'group'], 'products' => ['meat']],
        ])->call('create')->assertHasNoFormErrors();
        $species = AnimalSpecies::where('code', 'test_rabbit')->sole();
        $this->assertSame($typeId, (int) $species->legacy_livestock_type_id);
        $this->assertSame('live_birth', $species->capabilities_json['reproduction']);

        $this->publishBlueprint($species, 'record_birth', $this->birthForm());
        $this->assertSame('published', $species->fresh()->status);
        $this->assertSame('published', EventDefinition::where('code', 'farrowing')->value('status'));
        $this->assertManifestRequested('blueprint.published');
    }

    public function test_event_builder_shows_role_mapping_and_blocks_unmapped_behaviours(): void
    {
        $species = $this->species('live_birth');
        $blueprint = $this->draftBlueprint($species);
        $notesOnly = $this->publish('births.notes', [$this->field('notes', 'text')]);
        $births = $this->publish('births.full', $this->birthForm());

        Livewire::test(CreateEventDefinition::class)->fillForm([
            'blueprint_uuid' => $blueprint->uuid, 'code' => 'farrowing', 'name' => 'Farrowing',
            'form_uuid' => $notesOnly->uuid, 'behavior_code' => 'record_birth',
        ])->assertSee('Needs attention')->assertSee('Add a form field for “Born alive”.')
            ->call('create')->assertHasFormErrors(['behavior_code']);

        Livewire::test(CreateEventDefinition::class)->fillForm([
            'blueprint_uuid' => $blueprint->uuid, 'code' => 'farrowing', 'name' => 'Farrowing',
            'form_uuid' => $births->uuid, 'behavior_code' => 'record_birth',
        ])->assertSee('Mapped')->assertSee('born_alive')->call('create')->assertHasNoFormErrors();
        $this->assertSame('record_birth', EventDefinition::where('code', 'farrowing')->value('behavior_code'));
    }

    public function test_publication_blocks_behaviours_the_species_cannot_perform(): void
    {
        $species = $this->species('live_birth');
        $eggs = [$this->field('egg_count', 'integer', true)];
        try {
            $this->publishBlueprint($species, 'record_egg_or_clutch', $eggs);
            $this->fail('Egg records were published for a live-birth species.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('capabilities.reproduction: BEHAVIOR_CAPABILITY_MISSING', implode(' ', $exception->errors()['events']));
        }
    }

    public function test_preview_marks_fields_with_their_behaviour_input(): void
    {
        $blueprint = $this->publishBlueprint($this->species('live_birth'), 'record_birth', $this->birthForm(), publish: false);
        Livewire::test(PreviewAnimalBlueprint::class, ['record' => $blueprint->getRouteKey()])
            ->assertSuccessful()->assertSeeHtml('title="Behaviour input"')->assertSee('Born alive')
            ->assertSee('Each entry asks for:')->assertSee('Piglet weight')->assertSee('(kg)');
    }

    public function test_animal_page_shows_profile_family_summaries_and_timeline(): void
    {
        [$sow, $events] = $this->ledger();
        Livewire::test(ListAnimalSubjects::class)->assertCanSeeTableRecords([$sow]);
        $page = Livewire::test(ViewAnimalSubject::class, ['record' => $sow->id])->assertSuccessful()
            ->assertSee('Sow 7')->assertSee('EAR-7')->assertSee('Piglet A')->assertSee('Upgraded')
            ->assertSee('Farrowing')->assertSee('Born alive')->assertSee('1. Sex: Female, Piglet weight: 1.4 kg')
            ->assertSee('Voided')->assertSee('Wrong animal');
        $page->set('showVoided', false)->assertDontSee('Wrong animal');
        $this->assertSame(0, EventRecord::where('subject_uuid', $sow->uuid)->where('record_status', 'voided')->where('id', '!=', $events['void']->id)->count());
    }

    public function test_events_explorer_filters_by_family_and_renders_display_values(): void
    {
        [, $events] = $this->ledger();
        Livewire::test(ListEventRecords::class)->assertSuccessful()
            ->assertCanSeeTableRecords([$events['birth'], $events['weight']])
            ->filterTable('family', 'births')->assertCanSeeTableRecords([$events['birth']])->assertCanNotSeeTableRecords([$events['weight']])
            ->mountTableAction('details', $events['birth'])->assertSee('Born Alive')->assertSee('1. Sex: Female, Piglet weight: 1.4 kg')->assertSee('Births');
    }

    public function test_reports_read_summary_tables(): void
    {
        $this->ledger();
        $page = Livewire::test(AnimalReports::class)->assertSuccessful()->assertSee('Test pig');
        $page->call('setTab', 'reproduction')->assertSee('Mothers')->assertSeeHtml('data-report="reproduction"')->assertSee('Sow 7');
        $page->call('setTab', 'production')->assertSee('Milk')->assertSee('12.5 l');
        $page->call('setTab', 'events')->assertSee('Farrowing')->assertDontSee('Old Weighing');
    }

    public function test_family_authority_moves_one_step_at_a_time_and_publishes_configuration(): void
    {
        Livewire::test(LedgerMigration::class)->assertSuccessful()->assertSee('Births')->assertSee('Legacy tables')
            ->callAction('changeAuthority', ['authority' => 'ledger', 'reason' => 'Skip shadow'], ['family' => 'births', 'current' => 'legacy'])
            ->assertHasActionErrors(['authority']);

        Livewire::test(LedgerMigration::class)
            ->callAction('changeAuthority', ['authority' => 'ledger_shadow', 'reason' => 'Compare births'], ['family' => 'births', 'current' => 'legacy'])
            ->assertHasNoActionErrors();
        $this->assertSame('ledger_shadow', $this->authority('births'));
        $this->assertManifestRequested('family_authority.changed');
        $this->assertDatabaseHas('audit_logs', ['action' => 'family_authority.changed', 'reason' => 'Compare births']);

        $run = DB::table('platform_permissions')->where('code', 'upgrades.run')->value('id');
        DB::table('user_platform_permissions')->where('permission_id', $run)->delete();
        Livewire::test(LedgerMigration::class)->assertSuccessful()->assertActionHidden('changeAuthority')
            ->assertSee('needs the “Run platform upgrades” permission');
        DB::table('user_platform_permissions')->delete();
        Livewire::test(LedgerMigration::class)->assertForbidden();
    }

    public function test_switching_to_the_ledger_needs_a_passing_reconciliation(): void
    {
        DB::table('platform_feature_flags')->where('key', 'family.births.authority')->update(['value_json' => json_encode(['authority' => 'ledger_shadow'])]);
        $toLedger = fn () => Livewire::test(LedgerMigration::class)
            ->callAction('changeAuthority', ['authority' => 'ledger', 'reason' => 'Births match'], ['family' => 'births', 'current' => 'ledger_shadow']);

        $toLedger()->assertHasActionErrors(['authority']);
        Livewire::test(LedgerMigration::class)->assertSee('Not run');

        $reconcile = function (bool $valuesPass): void {
            $run = (string) Str::uuid();
            foreach (['birth_events.mapped' => true, 'birth_events.values' => $valuesPass] as $code => $passed) {
                DB::table('reconciliation_results')->insert([
                    'run_uuid' => $run, 'check_code' => $code, 'entity_family' => 'births', 'expected_count' => 7,
                    'actual_count' => $passed ? 7 : 5, 'passed' => $passed, 'details_json' => '{}', 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        };
        $reconcile(false);
        $toLedger()->assertHasActionErrors(['authority']);
        Livewire::test(LedgerMigration::class)->assertSee('1 failed')->assertSee('birth_events.values: expected 7, found 5');

        $reconcile(true);
        $toLedger()->assertHasNoActionErrors();
        $this->assertSame('ledger', $this->authority('births'));
        $this->assertManifestRequested('family_authority.changed');
    }

    public function test_retiring_needs_an_enforced_app_version(): void
    {
        DB::table('platform_feature_flags')->where('key', 'family.health.authority')->update(['value_json' => json_encode(['authority' => 'ledger'])]);
        DB::table('reconciliation_results')->insert([
            'run_uuid' => (string) Str::uuid(), 'check_code' => 'vaccinations.values', 'entity_family' => 'health', 'expected_count' => 1,
            'actual_count' => 1, 'passed' => true, 'details_json' => '{}', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $retire = fn () => Livewire::test(LedgerMigration::class)
            ->callAction('changeAuthority', ['authority' => 'retired', 'reason' => 'Health is on the ledger'], ['family' => 'health', 'current' => 'ledger']);

        $retire()->assertHasActionErrors(['authority']);
        Livewire::test(LedgerMigration::class)->assertSee('Not enforced')->assertSee('1.1.2')
            ->callAction('appVersion', ['version' => '1.2', 'enforced' => true, 'reason' => 'Retire health'])->assertHasActionErrors(['version']);
        Livewire::test(LedgerMigration::class)
            ->callAction('appVersion', ['version' => '1.2.0', 'enforced' => true, 'reason' => 'Retire health'])->assertHasNoActionErrors();
        $this->assertSame(['version' => '1.2.0'], json_decode(DB::table('platform_feature_flags')->where('key', 'app.minimum_version')->value('value_json'), true));
        $this->assertManifestRequested('app_version.changed');
        $this->assertDatabaseHas('audit_logs', ['action' => 'app_version.changed', 'reason' => 'Retire health']);

        $retire()->assertHasNoActionErrors();
        $this->assertSame('retired', $this->authority('health'));
        $this->assertManifestRequested('family_authority.changed');
        Livewire::test(LedgerMigration::class)->assertSee('Retired (ledger only, legacy read-only)')->assertSee('Enforced');

        Livewire::test(LedgerMigration::class)
            ->callAction('changeAuthority', ['authority' => 'ledger', 'reason' => 'Roll back'], ['family' => 'health', 'current' => 'retired'])
            ->assertHasNoActionErrors();
        $this->assertSame('ledger', $this->authority('health'));
        $this->assertManifestRequested('family_authority.changed');
    }

    public function test_reference_lists_are_edited_in_lookup_sets_and_written_back_to_the_legacy_table(): void
    {
        $cattleType = DB::table('livestock_types')->insertGetId(['name' => 'Cattle', 'created_at' => now(), 'updated_at' => now()]);
        $pigType = DB::table('livestock_types')->insertGetId(['name' => 'Swine', 'created_at' => now(), 'updated_at' => now()]);
        foreach (['cattle' => $cattleType, 'pig' => $pigType] as $code => $type) {
            AnimalSpecies::create(['code' => $code, 'name' => ucfirst($code), 'status' => 'published', 'default_subject_mode' => 'individual', 'legacy_livestock_type_id' => $type]);
        }
        $shared = DB::table('birth_types')->insertGetId(['name' => 'Normal', 'livestockTypeId' => null, 'created_at' => now(), 'updated_at' => now()]);
        $sets = [];
        foreach (['cattle', 'pig'] as $species) {
            $sets[$species] = LookupSet::create(['code' => "legacy.birth_types.$species", 'name' => "Birth types ($species)", 'version' => 1, 'status' => 'published', 'is_system' => true, 'is_extensible' => false, 'legacy_table' => 'birth_types']);
            $sets[$species]->options()->create(['code' => 'normal', 'label' => 'Normal', 'translations_json' => ['en' => 'Normal'], 'sort_order' => 1, 'status' => 'active', 'legacy_id' => $shared]);
        }
        $this->assertTrue(LookupSetResource::canEdit($sets['cattle']));
        $page = Livewire::test(EditLookupSet::class, ['record' => $sets['cattle']->getRouteKey()]);
        $options = $page->get('data.options');
        $key = array_key_first($options);
        $options[$key]['label'] = 'Normal birth';
        $options[$key]['translations_json'] = ['en' => 'Normal birth', 'sw' => 'Uzazi wa kawaida'];
        $options['new'] = ['uuid' => (string) Str::uuid(), 'code' => 'breech', 'label' => 'Breech', 'translations_json' => ['en' => 'Breech'], 'sort_order' => 2, 'status' => 'active'];
        $page->set('data.options', $options)->call('save')->assertHasNoFormErrors();

        $this->assertSame('Normal birth', DB::table('birth_types')->where('id', $shared)->value('name'));
        $this->assertSame('Normal birth', $sets['pig']->options()->where('legacy_id', $shared)->value('label'));
        $this->assertSame('normal', $sets['cattle']->options()->where('legacy_id', $shared)->value('code'));
        $breech = $sets['cattle']->options()->where('code', 'breech')->sole();
        $this->assertSame(['name' => 'Breech', 'livestockTypeId' => $cattleType], (array) DB::table('birth_types')->where('id', $breech->legacy_id)->first(['name', 'livestockTypeId']));
        $this->assertSame(0, $sets['pig']->options()->where('code', 'breech')->count());
        $this->assertManifestRequested('reference_list.saved');
    }

    /** @return array{0: AnimalSubject, 1: array<string, EventRecord>} */
    private function ledger(): array
    {
        $species = $this->species('live_birth');
        $blueprint = $this->publishBlueprint($species, 'record_birth', $this->birthForm());
        $birthEvent = EventDefinition::where('code', 'farrowing')->sole();
        $farm = (string) Str::uuid();
        $sow = $this->subject($species, $blueprint, $farm, 'Sow 7', 'female');
        $piglet = $this->subject($species, $blueprint, $farm, 'Piglet A', 'female', ['mother_uuid' => $sow->uuid]);
        DB::table('identifier_types')->insertOrIgnore(['uuid' => (string) Str::uuid(), 'code' => 'ear_tag_test', 'name' => 'Ear tag', 'supported_subject_modes' => '["individual"]', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('subject_identifiers')->insert(['uuid' => (string) Str::uuid(), 'subject_uuid' => $sow->uuid, 'identifier_type_uuid' => DB::table('identifier_types')->where('code', 'ear_tag_test')->value('uuid'),
            'normalized_value' => 'EAR7', 'display_value' => 'EAR-7', 'status' => 'active', 'assigned_at' => now(), 'revision' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('legacy_record_mappings')->insert(['source_table' => 'livestocks', 'source_key' => '41', 'target_type' => 'animal_subject', 'target_uuid' => $sow->uuid, 'created_at' => now(), 'updated_at' => now()]);
        $form = FormDefinition::where('uuid', $birthEvent->form_uuid)->sole();
        $event = fn (string $code, ?string $behavior, array $payload, string $status = 'submitted', ?string $reason = null) => EventRecord::create([
            'uuid' => (string) Str::uuid(), 'farm_uuid' => $farm, 'subject_uuid' => $sow->uuid,
            'event_definition_uuid' => $birthEvent->uuid, 'event_code' => $code, 'event_definition_version' => 1,
            'form_uuid' => $form->uuid, 'form_version' => $form->version, 'occurred_at' => now()->subDay(),
            'payload_json' => $payload, 'record_status' => $status, 'void_reason' => $reason, 'voided_at' => $reason ? now() : null,
            'behavior_code' => $behavior, 'revision' => 1, 'idempotency_uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $events = [
            'birth' => $event('farrowing', 'record_birth', ['born_alive' => 1, 'born_dead' => 0, 'offspring' => [['sex_code' => 'female', 'weight' => 1.4]]]),
            'weight' => $event('weighing', 'update_weight', ['notes' => 'Routine']),
            'void' => $event('old_weighing', 'update_weight', ['notes' => 'Mistake'], 'voided', 'Wrong animal'),
        ];
        DB::table('subject_reproduction_state')->insert(['subject_uuid' => $sow->uuid, 'farm_uuid' => $farm, 'births_count' => 1, 'total_born_alive' => 1, 'total_born_dead' => 0,
            'last_birth_at' => now()->subDay(), 'last_born_alive' => 1, 'last_born_dead' => 0, 'last_event_uuid' => $events['birth']->uuid, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('subject_production_daily')->insert(['subject_uuid' => $sow->uuid, 'farm_uuid' => $farm, 'production_date' => now()->toDateString(), 'product_code' => 'milk',
            'quantity' => 12.5, 'damaged_quantity' => 0, 'unit' => 'l', 'event_count' => 1, 'created_at' => now(), 'updated_at' => now()]);

        return [$sow, $events];
    }

    private function subject(AnimalSpecies $species, AnimalBlueprint $blueprint, string $farm, string $name, string $sex, array $attributes = []): AnimalSubject
    {
        return AnimalSubject::create([
            'uuid' => (string) Str::uuid(), 'farm_uuid' => $farm, 'species_uuid' => $species->uuid,
            'blueprint_uuid' => $blueprint->uuid, 'blueprint_version' => $blueprint->version, 'unit_type' => 'individual',
            'display_name' => $name, 'sex_code' => $sex, 'lifecycle_status' => 'active', 'attributes_json' => $attributes, 'revision' => 1,
        ]);
    }

    private function species(string $reproduction): AnimalSpecies
    {
        return AnimalSpecies::create(['code' => 'test_pig_'.Str::lower(Str::random(4)), 'name' => 'Test pig', 'default_subject_mode' => 'individual', 'status' => 'draft',
            'capabilities_json' => ['subjectModes' => ['individual'], 'reproduction' => $reproduction, 'products' => ['meat', 'milk']]]);
    }

    private function draftBlueprint(AnimalSpecies $species): AnimalBlueprint
    {
        $registration = $this->publish('registration.'.$species->code, [$this->field('notes', 'text')], 'registration');

        return AnimalBlueprint::create(['species_uuid' => $species->uuid, 'code' => $species->code.'.standard', 'version' => 1, 'status' => 'draft',
            'registration_form_uuid' => $registration->uuid, 'registration_form_version' => 1,
            'settings_json' => ['subjectModes' => ['individual'], 'identificationPolicy' => ['allowed' => [], 'required' => []]]]);
    }

    private function publishBlueprint(AnimalSpecies $species, string $behavior, array $fields, bool $publish = true): AnimalBlueprint
    {
        $blueprint = $this->draftBlueprint($species);
        $form = $this->publish('event.'.$species->code.'.'.$behavior, $fields);
        EventDefinition::create(['blueprint_uuid' => $blueprint->uuid, 'blueprint_version' => 1, 'code' => $behavior === 'record_birth' ? 'farrowing' : $behavior,
            'name' => $behavior === 'record_birth' ? 'Farrowing' : str($behavior)->headline(), 'form_uuid' => $form->uuid, 'form_version' => 1,
            'behavior_code' => $behavior, 'status' => 'draft', 'sort_order' => 1]);

        return $publish ? app(DynamicConfigurationService::class)->publishBlueprint($blueprint, auth()->user()) : $blueprint;
    }

    private function publish(string $code, array $fields, string $purpose = 'event'): FormDefinition
    {
        $form = FormDefinition::create(['code' => $code, 'version' => 1, 'purpose' => $purpose, 'status' => 'draft', 'schema_json' => [
            'presentation' => ['title' => ['en' => str($code)->headline()->toString()]],
            'sections' => [['key' => 'details', 'label' => ['en' => 'Details'], 'fields' => $fields]],
        ]]);

        return app(DynamicConfigurationService::class)->publishForm($form, auth()->user());
    }

    private function birthForm(): array
    {
        return [
            $this->field('born_alive', 'integer', true),
            $this->field('born_dead', 'integer'),
            $this->field('offspring', 'repeatable_group') + ['item_fields' => [
                ['key' => 'sex_code', 'label' => ['en' => 'Sex'], 'component' => 'single_select', 'required' => 'always',
                    'options' => [['value' => 'female', 'label' => ['en' => 'Female']], ['value' => 'male', 'label' => ['en' => 'Male']]]],
                ['key' => 'weight', 'label' => ['en' => 'Piglet weight'], 'component' => 'measurement', 'unit' => 'kg', 'required' => 'optional'],
            ]],
            $this->field('notes', 'text'),
        ];
    }

    private function field(string $key, string $component, bool $required = false): array
    {
        return [
            'field_uuid' => (string) Str::uuid(), 'key' => $key, 'label' => ['en' => str($key)->headline()->toString()],
            'component' => $component, 'value_type' => match ($component) {
                'integer' => 'integer', 'repeatable_group' => 'array', default => 'string'
            },
            'data_owner' => 'user_input', 'required' => $required ? 'always' : 'optional', 'nullable' => ! $required, 'sort_order' => 0,
        ];
    }

    private function authority(string $family): string
    {
        return json_decode(DB::table('platform_feature_flags')->where('key', "family.$family.authority")->value('value_json'), true)['authority'];
    }

    /** The backend compiles the manifest; the Portal only queues the request with the change. */
    private function assertManifestRequested(string $reason): void
    {
        $this->assertSame($reason, DB::table('configuration_publish_requests')->whereNull('fulfilled_at')->latest('id')->value('reason'));
    }
}
