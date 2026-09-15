<?php

namespace App\Filament\Resources\PrepuceConditions\Pages;

use App\Filament\Resources\PrepuceConditions\PrepuceConditionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPrepuceCondition extends EditRecord
{
    protected static string $resource = PrepuceConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

