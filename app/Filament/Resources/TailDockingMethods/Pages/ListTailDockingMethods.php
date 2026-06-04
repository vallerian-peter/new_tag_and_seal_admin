<?php

namespace App\Filament\Resources\TailDockingMethods\Pages;

use App\Filament\Resources\TailDockingMethods\TailDockingMethodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTailDockingMethods extends ListRecords
{
    protected static string $resource = TailDockingMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
