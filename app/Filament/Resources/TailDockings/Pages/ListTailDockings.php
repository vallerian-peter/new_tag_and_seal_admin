<?php

namespace App\Filament\Resources\TailDockings\Pages;

use App\Filament\Resources\TailDockings\TailDockingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTailDockings extends ListRecords
{
    protected static string $resource = TailDockingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

