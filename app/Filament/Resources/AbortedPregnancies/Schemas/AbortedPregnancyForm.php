<?php

namespace App\Filament\Resources\AbortedPregnancies\Schemas;

use App\Models\Livestock;
use App\Support\UuidHelper;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AbortedPregnancyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('uuid')
                            ->label('UUID')
                            ->default(function () {
                                // Format: timestamp-randomNumber-eventType
                                $timestamp = now()->timestamp;
                                $random = mt_rand(100000, 999999);
                                return "{$timestamp}-{$random}-abort";
                            })
                            ->readOnly()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Auto-generated: timestamp-random-eventType'),
                        
                        Select::make('farmUuid')
                            ->label('Farm')
                            ->relationship('farm', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->columnSpan(1)
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Clear livestock selection when farm changes
                                $set('livestockUuid', null);
                            }),
                        
                        Select::make('livestockUuid')
                            ->label('Livestock')
                            ->options(function (Get $get) {
                                $farmUuid = $get('farmUuid');
                                if (!$farmUuid) {
                                    return [];
                                }
                                
                                // Only show livestock from the selected farm
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
                            ->columnSpan(1)
                            ->disabled(fn (Get $get) => !$get('farmUuid'))
                            ->helperText(fn (Get $get) => !$get('farmUuid') ? 'Please select a farm first' : null),
                    ])
                    ->columns(2),
                
                Section::make('Abortion Details')
                    ->schema([
                        DatePicker::make('abortionDate')
                            ->label('Abortion Date')
                            ->native(false)
                            ->required()
                            ->maxDate(now())
                            ->columnSpan(1)
                            ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                            ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'resolved' => 'Resolved',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('active')
                            ->required()
                            ->columnSpan(1),
                        
                        Select::make('reproductiveProblemId')
                            ->label('Reproductive Problem')
                            ->relationship('reproductiveProblem', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->columnSpanFull()
                            ->helperText('Select the reproductive health issue that caused the abortion'),
                        
                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull()
                            ->placeholder('Add any additional notes or observations...'),
                    ])
                    ->columns(2),
            ]);
    }
}

