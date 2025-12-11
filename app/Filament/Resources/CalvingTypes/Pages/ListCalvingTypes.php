<?php

namespace App\Filament\Resources\CalvingTypes\Pages;

use App\Filament\Resources\CalvingTypes\CalvingTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalvingTypes extends ListRecords
{
    protected static string $resource = CalvingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

