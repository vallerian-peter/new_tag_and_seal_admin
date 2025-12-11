<?php

namespace App\Filament\Resources\IdentityCardTypes\Pages;

use App\Filament\Resources\IdentityCardTypes\IdentityCardTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIdentityCardTypes extends ListRecords
{
    protected static string $resource = IdentityCardTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
