<?php

namespace App\Filament\Resources\Feedings\Schemas;

use App\Models\Livestock;
use App\Support\UuidHelper;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class FeedingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->default(function () {
                        $timestamp = now()->timestamp;
                        $uuid = UuidHelper::generate();
                        return "{$timestamp}-{$uuid}-feeding";
                    })
                    ->readOnly()
                    ->required()
                    ->maxLength(255)
                    ->helperText('Auto-generated: timestamp-uuid-eventType'),
                Select::make('feedingTypeId')
                    ->label('Feeding Type')
                    ->relationship('feedingType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('livestockUuid', null);
                    }),
                Select::make('livestockUuid')
                    ->label('Livestock')
                    ->options(function (Get $get) {
                        $farmUuid = $get('farmUuid');
                        if (!$farmUuid) {
                            return [];
                        }
                        return Livestock::where('farmUuid', $farmUuid)
                            ->get()
                            ->mapWithKeys(function ($livestock) {
                                $label = trim(
                                    ($livestock->identificationNumber ?? $livestock->uuid) . 
                                    ($livestock->name ? " - {$livestock->name}" : '') .
                                    ($livestock->species ? " ({$livestock->species->name})" : '')
                                );
                                return [$livestock->uuid => $label];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->disabled(fn (Get $get) => !$get('farmUuid'))
                    ->helperText(fn (Get $get) => !$get('farmUuid') ? 'Please select a farm first' : null)
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->identificationNumber ?? $record->uuid) . ' ' . ($record->name ? "- {$record->name}" : '')))
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('nextFeedingTime')
                    ->label('Next Feeding Time')
                    ->afterStateHydrated(function (DateTimePicker $component, $state): void {
                        if (filled($state) && is_string($state)) {
                            $component->state(Carbon::parse($state));
                        }
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (blank($state)) {
                            return null;
                        }

                        if ($state instanceof Carbon) {
                            return $state->format('Y-m-d H:i:s');
                        }

                        return (string) $state;
                    })
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->reactive()
                    ->afterStateHydrated(function (TextInput $component, $state, callable $set): void {
                        if (blank($state) || ! is_string($state)) {
                            $set('amount_unit', 'kg');
                            return;
                        }

                        $matches = [];
                        if (preg_match('/^([\d\.]+)\s*(g|kg|ton)$/i', $state, $matches)) {
                            $component->state($matches[1]);
                            $set('amount_unit', strtolower($matches[2]));
                        } else {
                            $set('amount_unit', 'kg');
                        }
                    })
                    ->dehydrateStateUsing(function ($state, callable $get) {
                        if (blank($state)) {
                            return null;
                        }

                        $unit = $get('amount_unit') ?: 'kg';

                        return (string) $state . $unit;
                    })
                    ->nullable(),
                Select::make('amount_unit')
                    ->label('Unit')
                    ->options([
                        'g' => 'g',
                        'kg' => 'kg',
                        'ton' => 'ton',
                    ])
                    ->default('kg')
                    ->dehydrated(false)
                    ->required(),
                Textarea::make('remarks')
                    ->rows(3)
                    ->maxLength(65535)
                    ->nullable(),
            ]);
    }
}


