<?php

namespace App\Filament\Resources\LivestockMarkings\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LivestockMarkingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            EventLogFormHelpers::uuidField('livestock-marking'),
            EventLogFormHelpers::farmField(),
            EventLogFormHelpers::livestockField(),
            EventLogFormHelpers::eventDateField(),
            Select::make('markingType')
                ->label('Marking Type')
                ->options([
                    'ear_tag' => 'Ear Tag',
                    'tattoo' => 'Tattoo',
                    'branding' => 'Branding',
                    'paint' => 'Paint',
                    'other' => 'Other',
                ])
                ->default('other')
                ->required(),
            Textarea::make('description')->label('Description')->rows(2)->nullable(),
            Textarea::make('notes')->label('Notes')->rows(3)->nullable(),
        ]);
    }
}

