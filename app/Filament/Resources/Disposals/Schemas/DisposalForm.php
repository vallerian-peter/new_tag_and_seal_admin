<?php

namespace App\Filament\Resources\Disposals\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Support\UuidHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DisposalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('disposal'),
                EventLogFormHelpers::farmField(),
                EventLogFormHelpers::livestockField(),
                EventLogFormHelpers::eventDateField(),
                Select::make('disposalTypeId')
                    ->label('Disposal Type')
                    ->relationship('disposalType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('reasons')
                    ->label('Reasons')
                    ->rows(3)
                    ->required(),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->nullable(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->nullable(),
            ]);
    }
}

