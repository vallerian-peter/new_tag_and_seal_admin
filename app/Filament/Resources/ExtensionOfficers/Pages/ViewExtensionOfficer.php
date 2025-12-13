<?php

namespace App\Filament\Resources\ExtensionOfficers\Pages;

use App\Filament\Resources\ExtensionOfficers\ExtensionOfficerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExtensionOfficer extends ViewRecord
{
    protected static string $resource = ExtensionOfficerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
