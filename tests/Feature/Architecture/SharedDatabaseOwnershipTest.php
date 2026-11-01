<?php

namespace Tests\Feature\Architecture;

use Tests\TestCase;

class SharedDatabaseOwnershipTest extends TestCase
{
    public function test_portal_does_not_own_or_run_shared_database_migrations(): void
    {
        $portalMigrations = glob(database_path('migrations/*.php')) ?: [];
        $composer = json_decode((string) file_get_contents(base_path('composer.json')), true, flags: JSON_THROW_ON_ERROR);
        $scripts = json_encode($composer['scripts'] ?? [], JSON_THROW_ON_ERROR);

        $this->assertSame([], $portalMigrations);
        $this->assertStringNotContainsString('artisan migrate', $scripts);
    }
}
