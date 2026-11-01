<?php

namespace App\Filament\Resources\FormDefinitions\Pages;

use App\Filament\Resources\FormDefinitions\FormDefinitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFormDefinition extends CreateRecord
{
    protected static string $resource = FormDefinitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_uuid'] = auth()->user()->uuid;

        return $data;
    }
}
