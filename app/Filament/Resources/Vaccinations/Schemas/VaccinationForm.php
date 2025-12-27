<?php

namespace App\Filament\Resources\Vaccinations\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Models\ExtensionOfficer;
use App\Models\Vaccine;
use App\Models\Vet;
use App\Support\UuidHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class VaccinationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                EventLogFormHelpers::uuidField('vaccination'),
                TextInput::make('vaccinationNo')
                    ->label('Vaccination No.')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                EventLogFormHelpers::farmField()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Clear vaccine selection when farm changes
                        $set('vaccineUuid', null);
                    }),
                EventLogFormHelpers::livestockField()
                    ->preload()
                    ->required(),
                EventLogFormHelpers::eventDateField(),
                Select::make('vaccineUuid')
                    ->label('Vaccine')
                    ->options(function (Get $get) {
                        $farmUuid = $get('farmUuid');
                        if (!$farmUuid) {
                            return [];
                        }
                        // Only show vaccines from the selected farm
                        return Vaccine::where('farmUuid', $farmUuid)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(function ($vaccine) {
                                $label = $vaccine->name . ($vaccine->lot ? ' (Lot: ' . $vaccine->lot . ')' : '');
                                return [$vaccine->uuid => $label];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->disabled(fn (Get $get) => !$get('farmUuid'))
                    ->helperText(fn (Get $get) => !$get('farmUuid') ? 'Please select a farm first' : null),
                Select::make('diseaseId')
                    ->label('Disease')
                    ->relationship('disease', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('vetId')
                    ->label('Vet')
                    ->options(function () {
                        $vets = Vet::query()->orderBy('fullName')->get();
                        $options = $vets->mapWithKeys(function ($vet) {
                            return [$vet->medicalLicenseNo => $vet->fullName . ' (' . $vet->medicalLicenseNo . ')'];
                        })->toArray();
                        $options['__custom__'] = 'Enter Custom License Number';
                        return $options;
                    })
                    ->searchable()
                    ->nullable()
                    ->live()
                    ->afterStateHydrated(function ($state, callable $set, Get $get) {
                        // If vetId doesn't match any vet's license, treat it as custom
                        if ($state && !Vet::where('medicalLicenseNo', $state)->exists()) {
                            $set('vetId', '__custom__');
                            $set('vetIdCustom', $state);
                        }
                    })
                    ->dehydrateStateUsing(function ($state, Get $get) {
                        // If custom is selected, use the custom value
                        if ($state === '__custom__') {
                            return $get('vetIdCustom');
                        }
                        return $state;
                    })
                    ->helperText('Select a vet from the list or choose "Enter Custom License Number" to add a custom license.'),
                TextInput::make('vetIdCustom')
                    ->label('Custom Vet Medical License')
                    ->visible(fn (Get $get) => $get('vetId') === '__custom__')
                    ->required(fn (Get $get) => $get('vetId') === '__custom__')
                    ->helperText('Enter a custom medical license number.'),
                Select::make('extensionOfficerId')
                    ->label('Extension Officer')
                    ->options(function () {
                        $officers = ExtensionOfficer::query()->orderBy('fullName')->get();
                        $options = $officers->mapWithKeys(function ($officer) {
                            return [$officer->medicalLicenseNo => $officer->fullName . ' (' . $officer->medicalLicenseNo . ')'];
                        })->toArray();
                        $options['__custom__'] = 'Enter Custom License Number';
                        return $options;
                    })
                    ->searchable()
                    ->nullable()
                    ->live()
                    ->afterStateHydrated(function ($state, callable $set, Get $get) {
                        // If extensionOfficerId doesn't match any officer's license, treat it as custom
                        if ($state && !ExtensionOfficer::where('medicalLicenseNo', $state)->exists()) {
                            $set('extensionOfficerId', '__custom__');
                            $set('extensionOfficerIdCustom', $state);
                        }
                    })
                    ->dehydrateStateUsing(function ($state, Get $get) {
                        // If custom is selected, use the custom value
                        if ($state === '__custom__') {
                            return $get('extensionOfficerIdCustom');
                        }
                        return $state;
                    })
                    ->helperText('Select an extension officer from the list or choose "Enter Custom License Number" to add a custom license.'),
                TextInput::make('extensionOfficerIdCustom')
                    ->label('Custom Extension Officer Medical License')
                    ->visible(fn (Get $get) => $get('extensionOfficerId') === '__custom__')
                    ->required(fn (Get $get) => $get('extensionOfficerId') === '__custom__')
                    ->helperText('Enter a custom medical license number.'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->default('completed')
                    ->required(),
            ]);
    }
}

