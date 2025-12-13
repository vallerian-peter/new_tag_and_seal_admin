<?php

namespace App\Filament\Resources\ExtensionOfficers\Schemas;

use App\Models\District;
use App\Models\Region;
use App\Models\Ward;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExtensionOfficerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('firstName')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('middleName')
                    ->label('Middle Name')
                    ->maxLength(255),
                TextInput::make('lastName')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ExtensionOfficers\Pages\CreateExtensionOfficer)
                    ->dehydrated(fn ($state) => !empty($state))
                    ->maxLength(255),
                Select::make('gender')
                    ->label('Gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                TextInput::make('licenseNumber')
                    ->label('License Number')
                    ->maxLength(255),
                TextInput::make('address')
                    ->label('Address')
                    ->maxLength(500),
                Select::make('countryId')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Reset dependent fields when country changes
                        $set('regionId', null);
                        $set('districtId', null);
                        $set('wardId', null);
                    }),
                Select::make('regionId')
                    ->label('Region')
                    ->relationship('region', 'name', modifyQueryUsing: function ($query, $get) {
                        $countryId = $get('countryId');
                        if ($countryId) {
                            $query->where('countryId', $countryId);
                        } else {
                            // If no country selected, return empty query
                            $query->whereRaw('1 = 0');
                        }
                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Reset dependent fields when region changes
                        $set('districtId', null);
                        $set('wardId', null);
                    })
                    ->disabled(fn ($get) => !$get('countryId'))
                    ->helperText(fn ($get) => !$get('countryId') ? 'Please select a country first' : null),
                Select::make('districtId')
                    ->label('District')
                    ->relationship('district', 'name', modifyQueryUsing: function ($query, $get) {
                        $regionId = $get('regionId');
                        if ($regionId) {
                            $query->where('regionId', $regionId);
                        } else {
                            // If no region selected, return empty query
                            $query->whereRaw('1 = 0');
                        }
                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Reset ward when district changes
                        $set('wardId', null);
                    })
                    ->disabled(fn ($get) => !$get('regionId'))
                    ->helperText(fn ($get) => !$get('regionId') ? 'Please select a region first' : null),
                Select::make('wardId')
                    ->label('Ward')
                    ->relationship('ward', 'name', modifyQueryUsing: function ($query, $get) {
                        $districtId = $get('districtId');
                        if ($districtId) {
                            $query->where('districtId', $districtId);
                        } else {
                            // If no district selected, return empty query
                            $query->whereRaw('1 = 0');
                        }
                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->disabled(fn ($get) => !$get('districtId'))
                    ->helperText(fn ($get) => !$get('districtId') ? 'Please select a district first' : null),
                TextInput::make('organization')
                    ->label('Organization')
                    ->maxLength(255),
                Toggle::make('isVerified')
                    ->label('Is Verified')
                    ->default(false),
                Select::make('specialization')
                    ->label('Specialization')
                    ->options([
                        'livestock_management' => 'Livestock Management',
                        'medication_management' => 'Medication Management',
                        'vaccination_services' => 'Vaccination Services',
                        'animal_health' => 'Animal Health',
                        'disease_control' => 'Disease Control & Prevention',
                        'livestock_breeding' => 'Livestock Breeding',
                        'animal_nutrition' => 'Animal Nutrition & Feed Management',
                        'dairy_management' => 'Dairy Management',
                        'poultry_management' => 'Poultry Management',
                        'livestock_records' => 'Livestock Records & Data Management',
                        'reproductive_health' => 'Reproductive Health Management',
                        'deworming_services' => 'Deworming Services',
                        'veterinary_assistance' => 'Veterinary Assistance',
                        'livestock_husbandry' => 'Livestock Husbandry',
                        'animal_welfare' => 'Animal Welfare',
                        'breeding_technologies' => 'Breeding Technologies',
                        'milk_production' => 'Milk Production Management',
                        'livestock_feed' => 'Livestock Feed & Nutrition',
                        'disease_diagnosis' => 'Disease Diagnosis',
                        'treatment_administration' => 'Treatment Administration',
                        'livestock_monitoring' => 'Livestock Monitoring & Care',
                        'other' => 'Other',
                    ])
                    ->searchable()
                    ->preload()
                    ->helperText('Select the primary area of specialization in farm/livestock management'),
            ]);
    }
}
