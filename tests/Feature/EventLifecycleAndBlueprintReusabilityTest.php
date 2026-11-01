<?php

namespace Tests\Feature;

use App\Models\AnimalBlueprint;
use App\Models\AnimalSpecies;
use App\Models\AuditLog;
use App\Models\EventDefinition;
use App\Models\EventRecord;
use App\Models\FormDefinition;
use App\Models\User;
use App\Services\DynamicConfigurationService;
use App\Services\EventLifecycleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventLifecycleAndBlueprintReusabilityTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (glob(base_path('../new-tag-and-seal-laravel-backend/database/migrations/*.php')) as $migration) {
            (require $migration)->up();
        }
        $this->admin = User::create([
            'username' => 'Lifecycle Admin',
            'email' => 'lifecycle@example.test',
            'password' => 'secret123',
            'role' => 'systemUser',
            'roleId' => 0,
            'status' => 'active',
        ]);
        $this->actingAs($this->admin);
    }

    public function test_can_reuse_existing_event_on_blueprint(): void
    {
        $species = AnimalSpecies::create(['name' => 'Goat', 'code' => 'goat', 'status' => 'active']);
        $regForm = FormDefinition::create([
            'code' => 'goat_reg',
            'version' => 1,
            'purpose' => 'registration',
            'status' => 'published',
            'schema_json' => ['sections' => []],
        ]);

        $blueprint = AnimalBlueprint::create([
            'species_uuid' => $species->uuid,
            'code' => 'goat_standard',
            'version' => 1,
            'registration_form_uuid' => $regForm->uuid,
            'status' => 'draft',
            'settings_json' => [],
        ]);

        $eventForm = FormDefinition::create([
            'code' => 'weight_form',
            'version' => 1,
            'purpose' => 'event',
            'status' => 'published',
            'schema_json' => [
                'sections' => [
                    [
                        'key' => 'metrics',
                        'label' => ['en' => 'Metrics'],
                        'fields' => [
                            [
                                'field_uuid' => (string) Str::uuid(),
                                'key' => 'weight',
                                'label' => ['en' => 'Weight'],
                                'component' => 'measurement',
                                'value_type' => 'decimal',
                                'unit' => 'kg',
                                'data_owner' => 'user_input',
                                'required' => 'optional',
                                'nullable' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $existingEvent = EventDefinition::create([
            'blueprint_uuid' => $blueprint->uuid,
            'code' => 'record_weight',
            'name' => 'Weight Record',
            'form_uuid' => $eventForm->uuid,
            'behavior_code' => 'update_weight',
            'status' => 'draft',
        ]);

        // Second draft blueprint for sheep
        $sheep = AnimalSpecies::create(['name' => 'Sheep', 'code' => 'sheep', 'status' => 'active']);
        $sheepBlueprint = AnimalBlueprint::create([
            'species_uuid' => $sheep->uuid,
            'code' => 'sheep_standard',
            'version' => 1,
            'registration_form_uuid' => $regForm->uuid,
            'status' => 'draft',
            'settings_json' => [],
        ]);

        // Reuse the exact same Weight Record on Sheep
        $reusedEvent = app(DynamicConfigurationService::class)->reuseOrCloneEvent(
            $sheepBlueprint,
            $existingEvent,
            cloneForm: false,
            customName: 'Weight Record',
            actor: $this->admin
        );

        $this->assertSame($sheepBlueprint->uuid, $reusedEvent->blueprint_uuid);
        $this->assertSame($existingEvent->form_uuid, $reusedEvent->form_uuid);
        $this->assertSame('record_weight', $reusedEvent->code);
        $this->assertSame('Weight Record', $reusedEvent->name);
    }

    public function test_can_clone_event_form_with_custom_name_on_blueprint(): void
    {
        $species = AnimalSpecies::create(['name' => 'Pig', 'code' => 'pig', 'status' => 'active']);
        $regForm = FormDefinition::create([
            'code' => 'pig_reg',
            'version' => 1,
            'purpose' => 'registration',
            'status' => 'published',
            'schema_json' => ['sections' => []],
        ]);

        $blueprint = AnimalBlueprint::create([
            'species_uuid' => $species->uuid,
            'code' => 'pig_standard',
            'version' => 1,
            'registration_form_uuid' => $regForm->uuid,
            'status' => 'draft',
            'settings_json' => [],
        ]);

        $birthForm = FormDefinition::create([
            'code' => 'calving_form',
            'version' => 1,
            'purpose' => 'event',
            'status' => 'published',
            'schema_json' => [
                'sections' => [
                    [
                        'key' => 'litter',
                        'label' => ['en' => 'Litter'],
                        'fields' => [
                            [
                                'field_uuid' => (string) Str::uuid(),
                                'key' => 'born_alive',
                                'label' => ['en' => 'Born Alive'],
                                'component' => 'integer',
                                'value_type' => 'integer',
                                'data_owner' => 'user_input',
                                'required' => 'optional',
                                'nullable' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $calvingEvent = EventDefinition::create([
            'blueprint_uuid' => $blueprint->uuid,
            'code' => 'record_calving',
            'name' => 'Calving',
            'form_uuid' => $birthForm->uuid,
            'behavior_code' => 'record_birth',
            'status' => 'draft',
        ]);

        // Clone for pig as "Farrowing" with its own cloned form
        $farrowingEvent = app(DynamicConfigurationService::class)->reuseOrCloneEvent(
            $blueprint,
            $calvingEvent,
            cloneForm: true,
            customName: 'Farrowing',
            customCode: 'record_farrowing',
            actor: $this->admin
        );

        $this->assertSame('record_farrowing', $farrowingEvent->code);
        $this->assertSame('Farrowing', $farrowingEvent->name);
        $this->assertNotEquals($calvingEvent->form_uuid, $farrowingEvent->form_uuid);
        $this->assertSame('published', FormDefinition::where('uuid', $farrowingEvent->form_uuid)->value('status'));
    }

    public function test_can_archive_restore_and_force_delete_event_record(): void
    {
        $record = EventRecord::create([
            'farm_uuid' => (string) Str::uuid(),
            'subject_uuid' => (string) Str::uuid(),
            'event_definition_uuid' => (string) Str::uuid(),
            'event_definition_version' => 1,
            'form_uuid' => (string) Str::uuid(),
            'form_version' => 1,
            'event_code' => 'record_weight',
            'payload_json' => ['weight' => 250],
            'record_status' => 'confirmed',
            'revision' => 1,
            'occurred_at' => now(),
        ]);

        $lifecycle = app(EventLifecycleService::class);

        // 1. Archive
        $archived = $lifecycle->archive($record, 'Incorrect ear tag recorded', $this->admin);
        $this->assertSame('voided', $archived->record_status);
        $this->assertNotNull($archived->voided_at);
        $this->assertSame('Incorrect ear tag recorded', $archived->void_reason);
        $this->assertSame(2, $archived->revision);
        $this->assertTrue(AuditLog::where('action', 'event_record.archived')->where('entity_uuid', $record->uuid)->exists());

        // 2. Restore
        $restored = $lifecycle->restore($archived, 'Ear tag confirmed valid upon review', $this->admin);
        $this->assertSame('confirmed', $restored->record_status);
        $this->assertNull($restored->voided_at);
        $this->assertNull($restored->void_reason);
        $this->assertSame(3, $restored->revision);
        $this->assertTrue(AuditLog::where('action', 'event_record.restored')->where('entity_uuid', $record->uuid)->exists());

        // 3. Force Delete
        $archivedAgain = $lifecycle->archive($restored, 'Testing deletion');
        $deleted = $lifecycle->forceDelete($archivedAgain, 'Purged invalid test event', $this->admin);
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('event_records', ['uuid' => $record->uuid]);
        $this->assertTrue(AuditLog::where('action', 'event_record.permanently_deleted')->where('entity_uuid', $record->uuid)->exists());
    }
}
