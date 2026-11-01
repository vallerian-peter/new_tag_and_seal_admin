<?php

namespace Tests\Feature;

use App\Filament\Widgets\LivestockDataStats;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class LivestockDataStatsWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (glob(base_path('../new-tag-and-seal-laravel-backend/database/migrations/*.php')) as $migration) {
            (require $migration)->up();
        }
        $user = User::create([
            'username' => 'Stats Tester',
            'email' => 'stats@example.test',
            'password' => 'secret-test',
            'role' => 'systemUser',
            'roleId' => 0,
            'status' => 'active',
        ]);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        parent::tearDown();
    }

    public function test_livestock_data_stats_widget_mounts_and_renders_without_route_exceptions(): void
    {
        Livewire::test(LivestockDataStats::class)
            ->assertSuccessful()
            ->assertSee('Feedings')
            ->assertSee('Dewormings')
            ->assertSee('Vaccinations')
            ->assertSee('Milkings');
    }
}
