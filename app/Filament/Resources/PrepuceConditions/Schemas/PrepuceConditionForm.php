<?php

namespace App\Filament\Resources\PrepuceConditions\Schemas;

use App\Filament\Resources\Helpers\EventLogFormHelpers;
use App\Models\ExtensionOfficer;
use App\Models\PrepuceClinicalSign;
use App\Models\PrepuceTreatmentGiven;
use App\Models\Vet;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PrepuceConditionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            EventLogFormHelpers::uuidField('prepuce'),
            EventLogFormHelpers::farmField(),
            EventLogFormHelpers::livestockField(),
            EventLogFormHelpers::eventDateField(),

            Select::make('conditionTypeId')
                ->label('Condition Type')
                ->relationship('conditionType', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('severityId')
                ->label('Severity')
                ->relationship('severity', 'name')
                ->searchable()
                ->preload()
                ->required(),

            CheckboxList::make('clinicalSignIds')
                ->label('Clinical Signs')
                ->options(fn () => PrepuceClinicalSign::query()->orderBy('name')->pluck('name', 'id')->all())
                ->columns(2)
                ->nullable()
                ->afterStateHydrated(function ($component, $state) {
                    if (is_array($state)) {
                        $component->state(array_map('intval', $state));
                    }
                })
                ->dehydrateStateUsing(fn (?array $state) => empty($state) ? null : array_values(array_map('intval', $state))),

            Select::make('causeRiskId')
                ->label('Cause / Risk')
                ->relationship('causeRisk', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            CheckboxList::make('treatmentGivenIds')
                ->label('Treatment Given')
                ->options(fn () => PrepuceTreatmentGiven::query()->orderBy('name')->pluck('name', 'id')->all())
                ->columns(2)
                ->nullable()
                ->afterStateHydrated(function ($component, $state) {
                    if (is_array($state)) {
                        $component->state(array_map('intval', $state));
                    }
                })
                ->dehydrateStateUsing(fn (?array $state) => empty($state) ? null : array_values(array_map('intval', $state))),

            Select::make('administrationRouteId')
                ->label('Administration Route')
                ->relationship('administrationRoute', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            Select::make('medicineId')
                ->label('Medicine')
                ->relationship('medicine', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            Select::make('vetId')
                ->label('Vet')
                ->options(function () {
                    $vets = Vet::query()->orderBy('fullName')->get();

                    $options = $vets->mapWithKeys(function ($vet) {
                        return [$vet->medicalLicenseNo => $vet->fullName.' ('.$vet->medicalLicenseNo.')'];
                    })->toArray();

                    $options['__custom__'] = 'Enter Custom License Number';

                    return $options;
                })
                ->searchable()
                ->nullable()
                ->live()
                ->afterStateHydrated(function ($state, callable $set) {
                    if ($state && ! Vet::where('medicalLicenseNo', $state)->exists()) {
                        $set('vetId', '__custom__');
                        $set('vetIdCustom', $state);
                    }
                })
                ->dehydrateStateUsing(function ($state, Get $get) {
                    if ($state === '__custom__') {
                        return $get('vetIdCustom');
                    }

                    return $state;
                }),

            TextInput::make('vetIdCustom')
                ->label('Custom Vet Medical License')
                ->visible(fn (Get $get) => $get('vetId') === '__custom__')
                ->required(fn (Get $get) => $get('vetId') === '__custom__'),

            Select::make('extensionOfficerId')
                ->label('Extension Officer')
                ->options(function () {
                    $officers = ExtensionOfficer::query()->orderBy('fullName')->get();

                    $options = $officers->mapWithKeys(function ($officer) {
                        return [$officer->medicalLicenseNo => $officer->fullName.' ('.$officer->medicalLicenseNo.')'];
                    })->toArray();

                    $options['__custom__'] = 'Enter Custom License Number';

                    return $options;
                })
                ->searchable()
                ->nullable()
                ->live()
                ->afterStateHydrated(function ($state, callable $set) {
                    if ($state && ! ExtensionOfficer::where('medicalLicenseNo', $state)->exists()) {
                        $set('extensionOfficerId', '__custom__');
                        $set('extensionOfficerIdCustom', $state);
                    }
                })
                ->dehydrateStateUsing(function ($state, Get $get) {
                    if ($state === '__custom__') {
                        return $get('extensionOfficerIdCustom');
                    }

                    return $state;
                }),

            TextInput::make('extensionOfficerIdCustom')
                ->label('Custom Extension Officer Medical License')
                ->visible(fn (Get $get) => $get('extensionOfficerId') === '__custom__')
                ->required(fn (Get $get) => $get('extensionOfficerId') === '__custom__'),

            TextInput::make('quantity')
                ->label('Quantity')
                ->nullable(),

            TextInput::make('dose')
                ->label('Dose')
                ->nullable(),

            Select::make('breedingStatusId')
                ->label('Breeding Status')
                ->relationship('breedingStatus', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('healingStatusId')
                ->label('Healing Status')
                ->relationship('healingStatus', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            DatePicker::make('followUpDate')
                ->label('Follow-up Date')
                ->nullable(),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(3)
                ->nullable(),
        ]);
    }
}
