<?php

namespace App\Filament\Resources\IronInjections\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IronInjectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            EventLogFormHelpers::uuidField('iron-injection'),
            EventLogFormHelpers::farmField(),
            EventLogFormHelpers::livestockField(),
            EventLogFormHelpers::eventDateField(),
            TextInput::make('dosage')->label('Dosage')->nullable(),
            Select::make('medicineId')
                ->label('Medicine')
                ->relationship('medicine', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            Textarea::make('notes')->label('Notes')->rows(3)->nullable(),
        ]);
    }
}

