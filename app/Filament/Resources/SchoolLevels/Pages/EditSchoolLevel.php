<?php

namespace App\Filament\Resources\SchoolLevels\Pages;

use App\Filament\Resources\SchoolLevels\SchoolLevelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchoolLevel extends EditRecord
{
    protected static string $resource = SchoolLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
