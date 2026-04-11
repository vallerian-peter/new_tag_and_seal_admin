<?php

namespace App\Filament\Resources\TeethClippings\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Models\TeethClippingMethod;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeethClippingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            EventLogFormHelpers::uuidField('teeth-clipping'),
            EventLogFormHelpers::farmField(),
            EventLogFormHelpers::livestockField(),
            EventLogFormHelpers::eventDateField(),
            Select::make('method')
                ->label('Method')
                ->options(fn () => TeethClippingMethod::query()->orderBy('name')->pluck('name', 'name')->toArray())
                ->searchable()
                ->nullable(),
            Textarea::make('notes')
                ->label('Notes')
                ->rows(3)
                ->nullable(),
        ]);
    }
}

