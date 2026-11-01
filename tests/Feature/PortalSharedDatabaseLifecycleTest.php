<?php

namespace Tests\Feature;

use App\Filament\Resources\AnimalSubjects\Pages\ListAnimalSubjects;
use App\Models\AnimalSubject;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/** Run via scripts/test_dynamic_flow.py, on the database the backend wrote the device's animal to. */
class PortalSharedDatabaseLifecycleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $path = getenv('DYNAMIC_PORTAL_DATABASE_FIXTURE');
        if (! $path) {
            $this->markTestSkipped('Run the cross-app acceptance command to provide a temporary shared database.');
        }
        $realPath = realpath($path);
        $this->assertNotFalse($realPath);
        $this->assertStringStartsWith(realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR, $realPath);
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $realPath]);
        DB::purge('sqlite');
        $this->actingAs(User::where('email', 'portal@example.test')->sole());
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    public function test_portal_archives_and_restores_the_device_animal_and_queues_both_for_the_feed(): void
    {
        $subject = AnimalSubject::sole();
        $revision = (int) $subject->revision;

        Livewire::test(ListAnimalSubjects::class)
            ->callTableAction('archive', $subject, ['reason' => 'Registered twice on the farm'])->assertHasNoTableActionErrors();
        $this->assertSoftDeleted('animal_subjects', ['uuid' => $subject->uuid]);
        $this->assertDatabaseHas('sync_tombstones', ['entity_uuid' => $subject->uuid, 'superseded_at' => null]);

        Livewire::test(ListAnimalSubjects::class)->filterTable('trashed', true)
            ->callTableAction('restore', AnimalSubject::withTrashed()->sole())->assertHasNoTableActionErrors();
        $this->assertNotSoftDeleted('animal_subjects', ['uuid' => $subject->uuid]);
        $this->assertSame(0, DB::table('sync_tombstones')->where('entity_uuid', $subject->uuid)->whereNull('superseded_at')->count());

        $this->assertSame(
            [['action' => 'delete', 'revision' => $revision + 1], ['action' => 'upsert', 'revision' => $revision + 2]],
            DB::table('sync_change_requests')->where('entity_uuid', $subject->uuid)->orderBy('id')->get(['action', 'revision'])
                ->map(fn ($row) => ['action' => $row->action, 'revision' => (int) $row->revision])->all(),
        );
    }
}
