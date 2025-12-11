<?php

namespace App\Filament\Resources\BirthEvents\Pages;

use App\Filament\Resources\BirthEvents\BirthEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBirthEvents extends ListRecords
{
    protected static string $resource = BirthEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

