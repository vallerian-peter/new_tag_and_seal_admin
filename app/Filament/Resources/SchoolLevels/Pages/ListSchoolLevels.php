<?php

namespace App\Filament\Resources\SchoolLevels\Pages;

use App\Filament\Resources\SchoolLevels\SchoolLevelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchoolLevels extends ListRecords
{
    protected static string $resource = SchoolLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
