<?php

namespace App\Filament\Resources\LegalStatuses\Pages;

use App\Filament\Resources\LegalStatuses\LegalStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLegalStatus extends EditRecord
{
    protected static string $resource = LegalStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
