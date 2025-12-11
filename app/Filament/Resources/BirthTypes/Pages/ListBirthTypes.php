<?php

namespace App\Filament\Resources\BirthTypes\Pages;

use App\Filament\Resources\BirthTypes\BirthTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBirthTypes extends ListRecords
{
    protected static string $resource = BirthTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

