<?php

namespace App\Filament\Resources\AbortedPregnancies\Pages;

use App\Filament\Resources\AbortedPregnancies\AbortedPregnancyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAbortedPregnancies extends ListRecords
{
    protected static string $resource = AbortedPregnancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

