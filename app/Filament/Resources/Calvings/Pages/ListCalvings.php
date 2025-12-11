<?php

namespace App\Filament\Resources\Calvings\Pages;

use App\Filament\Resources\Calvings\CalvingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalvings extends ListRecords
{
    protected static string $resource = CalvingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

