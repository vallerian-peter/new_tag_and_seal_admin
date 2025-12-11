<?php

namespace App\Filament\Resources\SystemUsers\Pages;

use App\Filament\Resources\SystemUsers\SystemUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSystemUser extends EditRecord
{
    protected static string $resource = SystemUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
