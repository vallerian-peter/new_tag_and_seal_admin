<?php

it('does not expose destructive livestock actions or cascade history deletion', function () {
    $files = [
        app_path('Filament/Resources/Livestocks/Tables/LivestocksTable.php'),
        app_path('Filament/Resources/Livestocks/Pages/EditLivestock.php'),
    ];

    foreach ($files as $file) {
        $source = file_get_contents($file);

        expect($source)
            ->not->toContain('DeleteAction')
            ->not->toContain('DeleteBulkAction')
            ->not->toContain('deleteLivestockLogs')
            ->not->toContain("where('livestockUuid'");
    }
});

it('does not expose destructive farm or farmer actions', function () {
    $files = [
        app_path('Filament/Resources/Farms/Pages/EditFarm.php'),
        app_path('Filament/Resources/Farms/Tables/FarmsTable.php'),
        app_path('Filament/Resources/Farmers/Pages/EditFarmer.php'),
        app_path('Filament/Resources/Farmers/Tables/FarmersTable.php'),
    ];

    foreach ($files as $file) {
        $source = file_get_contents($file);

        expect($source)
            ->not->toContain('DeleteAction')
            ->not->toContain('DeleteBulkAction')
            ->not->toContain('deleteLivestockLogs')
            ->not->toContain('deleteFarmRelatedData');
    }
});

it('provides audited livestock archive trash and restore actions', function () {
    $table = file_get_contents(
        app_path('Filament/Resources/Livestocks/Tables/LivestocksTable.php')
    );
    $service = file_get_contents(app_path('Services/LivestockLifecycleService.php'));

    expect($table)
        ->toContain('TrashedFilter::make()')
        ->toContain("Action::make('archive')")
        ->toContain('RestoreAction::make()')
        ->toContain("Textarea::make('delete_reason')")
        ->not->toContain('ForceDeleteAction');

    expect($service)
        ->toContain("'livestock.deleted'")
        ->toContain("'livestock.restored'")
        ->toContain('publishDeletionTombstone');
});

it('globally freezes destructive filament actions until lifecycle policies exist', function () {
    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));

    expect($provider)
        ->toContain('DeleteAction::configureUsing')
        ->toContain('DeleteBulkAction::configureUsing')
        ->toContain('ForceDeleteAction::configureUsing')
        ->toContain('ForceDeleteBulkAction::configureUsing');
});
