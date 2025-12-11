<?php

namespace App\Filament\Resources\Pregnancies\Pages;

use App\Filament\Resources\Pregnancies\PregnancyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPregnancies extends ListRecords
{
    protected static string $resource = PregnancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

