<?php

namespace App\Filament\Resources\BirthEvents\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Models\BirthType;
use App\Models\BirthProblem;
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

class BirthEventForm
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
                                // This keeps it under 36 characters to fit uuid column
                                $timestamp = now()->timestamp;
                                $random = mt_rand(100000, 999999);
                                return "{$timestamp}-{$random}-birth";
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
                                $set('eventType', 'calving'); // Reset to default
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
                            ->live()
                            ->columnSpan(1)
                            ->disabled(fn (Get $get) => !$get('farmUuid'))
                            ->helperText(fn (Get $get) => !$get('farmUuid') ? 'Please select a farm first' : null)
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-detect event type based on livestock species
                                if ($state) {
                                    $livestock = Livestock::where('uuid', $state)->first();
                                    if ($livestock && $livestock->species) {
                                        $eventType = strtolower($livestock->species->name) === 'pig' ? 'farrowing' : 'calving';
                                        $set('eventType', $eventType);
                                    }
                                }
                            }),
                        
                        Select::make('eventType')
                            ->label('Event Type')
                            ->options([
                                'calving' => 'Calving (Cattle)',
                                'farrowing' => 'Farrowing (Pig)',
                            ])
                            ->required()
                            ->default('calving')
                            ->live()
                            ->columnSpanFull(),
                        
                        EventLogFormHelpers::eventDateField()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Event Details')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Start Date')
                            ->native(false)
                            ->nullable()
                            ->columnSpan(1)
                            ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                            ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                        
                        DatePicker::make('endDate')
                            ->label('End Date')
                            ->native(false)
                            ->nullable()
                            ->columnSpan(1)
                            ->afterStateHydrated(fn (DatePicker $component, $state) => $component->state(blank($state) ? null : Carbon::parse($state)))
                            ->dehydrateStateUsing(fn ($state) => $state instanceof Carbon ? $state->format('Y-m-d') : $state),
                        
                        Select::make('birthTypeId')
                            ->label(fn (Get $get) => $get('eventType') === 'farrowing' ? 'Farrowing Type' : 'Birth Type')
                            ->options(function (Get $get) {
                                $livestockUuid = $get('livestockUuid');
                                if (!$livestockUuid) {
                                    return BirthType::pluck('name', 'id');
                                }
                                
                                $livestock = Livestock::where('uuid', $livestockUuid)->first();
                                if (!$livestock || !$livestock->livestockTypeId) {
                                    return BirthType::pluck('name', 'id');
                                }
                                
                                return BirthType::where('livestockTypeId', $livestock->livestockTypeId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->columnSpan(1)
                            ->helperText(fn (Get $get) => $get('eventType') === 'farrowing' ? 'Select the type of farrowing (e.g., Assisted, Normal)' : 'Select the type of birth (e.g., Assisted, Normal)'),
                        
                        Select::make('birthProblemsId')
                            ->label(fn (Get $get) => $get('eventType') === 'farrowing' ? 'Farrowing Problem' : 'Birth Problem')
                            ->options(function (Get $get) {
                                $livestockUuid = $get('livestockUuid');
                                if (!$livestockUuid) {
                                    return BirthProblem::pluck('name', 'id');
                                }
                                
                                $livestock = Livestock::where('uuid', $livestockUuid)->first();
                                if (!$livestock || !$livestock->livestockTypeId) {
                                    return BirthProblem::pluck('name', 'id');
                                }
                                
                                return BirthProblem::where('livestockTypeId', $livestock->livestockTypeId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->columnSpan(1)
                            ->helperText('Any problems encountered during birth?'),
                        
                        Select::make('reproductiveProblemId')
                            ->label('Reproductive Problem')
                            ->relationship('reproductiveProblem', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->columnSpan(1)
                            ->helperText('General reproductive health issues'),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('active')
                            ->required()
                            ->columnSpan(1),
                        
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

