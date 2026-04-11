<?php

namespace App\Filament\Resources\StageChanges\Pages;

use App\Filament\Resources\StageChanges\StageChangeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStageChanges extends ListRecords
{
    protected static string $resource = StageChangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

