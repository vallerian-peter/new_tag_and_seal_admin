<?php

namespace App\Filament\Resources\StageChanges\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StageChangeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            EventLogFormHelpers::uuidField('stage-change'),
            EventLogFormHelpers::farmField(),
            EventLogFormHelpers::livestockField(),
            EventLogFormHelpers::eventDateField(),
            Select::make('fromStageId')
                ->label('From Stage')
                ->relationship('fromStage', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('toStageId')
                ->label('To Stage')
                ->relationship('toStage', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            Textarea::make('notes')->label('Notes')->rows(3)->nullable(),
        ]);
    }
}

