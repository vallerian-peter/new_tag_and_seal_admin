<?php

namespace App\Filament\Resources\FarmUsers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;


class FarmUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Farm & Role')
                    ->schema([
                        Select::make('farmUuid')
                            ->label('Farm')
                            ->relationship('farm', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => trim(
                                    ($record->name ?? $record->uuid) .
                                    ' (' . ($record->referenceNo ?? 'N/A') . ')'
                                )
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Select::make('roleTitle')
                            ->label('Role')
                            ->options([
                                'farm-manager' => 'Farm Manager',
                                'feeding-user' => 'Feeding User',
                                'weight-change-user' => 'Weight Change User',
                                'deworming-user' => 'Deworming User',
                                'medication-user' => 'Medication User',
                                'vaccination-user' => 'Vaccination User',
                                'disposal-user' => 'Disposal User',
                                'birth-event-user' => 'Birth Event User',
                                'aborted-pregnancy-user' => 'Aborted Pregnancy User',
                                'dryoff-user' => 'Dry-off User',
                                'insemination-user' => 'Insemination User',
                                'pregnancy-user' => 'Pregnancy User',
                                'milking-user' => 'Milking User',
                                'transfer-user' => 'Transfer User',
                            ])
                            ->required()
                            ->searchable()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('firstName')
                            ->label('First Name')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('middleName')
                            ->label('Middle Name')
                            ->nullable()
                            ->columnSpan(1),
                        TextInput::make('lastName')
                            ->label('Last Name')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->columnSpan(1),
                        Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ]);
    }
}
