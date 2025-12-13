<?php

namespace App\Filament\Resources\ExtensionOfficerFarmInvites\Pages;

use App\Filament\Resources\ExtensionOfficerFarmInvites\ExtensionOfficerFarmInviteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtensionOfficerFarmInvites extends ListRecords
{
    protected static string $resource = ExtensionOfficerFarmInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
