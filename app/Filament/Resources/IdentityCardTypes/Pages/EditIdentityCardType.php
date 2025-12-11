<?php

namespace App\Filament\Resources\IdentityCardTypes\Pages;

use App\Filament\Resources\IdentityCardTypes\IdentityCardTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIdentityCardType extends EditRecord
{
    protected static string $resource = IdentityCardTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
