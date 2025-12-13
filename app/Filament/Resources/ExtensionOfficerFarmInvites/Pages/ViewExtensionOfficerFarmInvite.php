<?php

namespace App\Filament\Resources\ExtensionOfficerFarmInvites\Pages;

use App\Filament\Resources\ExtensionOfficerFarmInvites\ExtensionOfficerFarmInviteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExtensionOfficerFarmInvite extends ViewRecord
{
    protected static string $resource = ExtensionOfficerFarmInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
