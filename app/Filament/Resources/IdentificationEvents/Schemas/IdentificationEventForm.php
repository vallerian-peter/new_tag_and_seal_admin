<?php

namespace App\Filament\Resources\IdentificationEvents\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IdentificationEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            EventLogFormHelpers::uuidField('identification-event'),
            EventLogFormHelpers::farmField(),
            EventLogFormHelpers::livestockField(),
            EventLogFormHelpers::eventDateField(),
            TextInput::make('identificationNumber')
                ->label('Identification Number')
                ->nullable(),
            Textarea::make('remarks')
                ->label('Remarks')
                ->rows(3)
                ->nullable(),
        ]);
    }
}

