<?php

namespace App\Filament\Resources\TailDockings\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Models\TailDockingMethod;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TailDockingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            EventLogFormHelpers::uuidField('tail-docking'),
            EventLogFormHelpers::farmField(),
            EventLogFormHelpers::livestockField(),
            EventLogFormHelpers::eventDateField(),
            Select::make('method')
                ->label('Method')
                ->options(fn () => TailDockingMethod::query()->orderBy('name')->pluck('name', 'name')->toArray())
                ->searchable()
                ->preload()
                ->nullable(),
            Textarea::make('notes')->label('Notes')->rows(3)->nullable(),
        ]);
    }
}
