<?php

namespace Tests\Feature;

use App\Filament\Resources\Livestocks\Pages\ListLivestocks;
use App\Models\AuditLog;
use App\Models\Livestock;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class LivestockLifecycleTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Never migrate or truncate the shared application database in UI tests.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (glob(base_path('../new-tag-and-seal-laravel-backend/database/migrations/*.php')) as $migration) {
            (require $migration)->up();
        }
        $this->admin = User::create(['username' => 'Portal tester', 'email' => 'portal@example.test', 'password' => 'test-password', 'role' => 'systemUser', 'roleId' => 0, 'status' => 'active']);
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        parent::tearDown();
    }

    public function test_archive_and_restore_keep_history_tombstone_the_animal_and_are_audited(): void
    {
        $livestock = $this->livestock();
        DB::table('identification_events')->insert([
            'uuid' => (string) Str::uuid(), 'farmUuid' => $livestock->farmUuid, 'livestockUuid' => $livestock->uuid,
            'identificationNumber' => $livestock->identificationNumber, 'eventDate' => now(),
            'remarks' => 'History must survive archive and restore.', 'created_at' => now(), 'updated_at' => now(),
        ]);

        Livewire::test(ListLivestocks::class)
            ->callTableAction('archive', $livestock, ['delete_reason' => 'Short'])->assertHasTableActionErrors(['delete_reason']);
        $this->assertNotSoftDeleted('livestocks', ['id' => $livestock->id]);
        $this->assertDatabaseCount('audit_logs', 0);

        Livewire::test(ListLivestocks::class)
            ->callTableAction('archive', $livestock, ['delete_reason' => 'Duplicate registration confirmed by administrator.'])
            ->assertHasNoTableActionErrors();
        $this->assertSoftDeleted('livestocks', ['id' => $livestock->id]);
        $this->assertDatabaseHas('livestocks', ['id' => $livestock->id, 'deleted_by_uuid' => $this->admin->uuid, 'revision' => 2,
            'delete_reason' => 'Duplicate registration confirmed by administrator.']);
        $this->assertDatabaseHas('sync_tombstones', ['entity_type' => 'livestock', 'entity_uuid' => $livestock->uuid,
            'farm_uuid' => $livestock->farmUuid, 'entity_revision' => 2, 'operation' => 'deleted', 'superseded_at' => null]);
        $this->assertDatabaseHas('identification_events', ['livestockUuid' => $livestock->uuid]);

        Livewire::test(ListLivestocks::class)->filterTable('trashed', true)
            ->callTableAction('restore', Livestock::withTrashed()->findOrFail($livestock->id))->assertHasNoTableActionErrors();
        $restored = Livestock::findOrFail($livestock->id);
        $this->assertSame($this->admin->uuid, $restored->restored_by_uuid);
        $this->assertSame(3, (int) $restored->revision);
        $this->assertNotNull(DB::table('sync_tombstones')->where('entity_uuid', $livestock->uuid)->value('superseded_at'));
        $this->assertDatabaseHas('identification_events', ['livestockUuid' => $livestock->uuid]);
        $this->assertSame(['livestock.deleted', 'livestock.restored'], AuditLog::query()->orderBy('id')->pluck('action')->all());
    }

    private function livestock(): Livestock
    {
        $type = DB::table('livestock_types')->insertGetId(['name' => 'Cattle', 'created_at' => now(), 'updated_at' => now()]);
        $species = DB::table('species')->insertGetId(['name' => 'Cattle', 'livestockTypeId' => $type, 'created_at' => now(), 'updated_at' => now()]);
        $breed = DB::table('breeds')->insertGetId(['name' => 'Test Breed', 'group' => 'Test', 'livestockTypeId' => $type, 'created_at' => now(), 'updated_at' => now()]);

        return Livestock::create([
            'farmUuid' => (string) Str::uuid(), 'uuid' => (string) Str::uuid(), 'identificationNumber' => 'TEST-ANIMAL-001',
            'livestockTypeId' => $type, 'name' => 'Protected animal', 'dateOfBirth' => '2025-01-01', 'gender' => 'female',
            'breedId' => $breed, 'speciesId' => $species, 'status' => 'active', 'weightAsOnRegistration' => '100',
        ]);
    }
}
