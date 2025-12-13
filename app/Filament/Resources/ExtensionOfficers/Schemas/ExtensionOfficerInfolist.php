<?php

namespace App\Filament\Resources\ExtensionOfficers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExtensionOfficerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('firstName')
                    ->label('First Name'),
                TextEntry::make('middleName')
                    ->label('Middle Name')
                    ->placeholder('-'),
                TextEntry::make('lastName')
                    ->label('Last Name'),
                TextEntry::make('email')
                    ->label('Email Address')
                    ->icon('heroicon-o-envelope'),
                TextEntry::make('phone')
                    ->label('Phone Number')
                    ->icon('heroicon-o-phone'),
                TextEntry::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('address')
                    ->label('Address')
                    ->placeholder('-'),
                TextEntry::make('country.name')
                    ->label('Country'),
                TextEntry::make('region.name')
                    ->label('Region'),
                TextEntry::make('district.name')
                    ->label('District'),
                TextEntry::make('ward.name')
                    ->label('Ward')
                    ->placeholder('-'),
                TextEntry::make('organization')
                    ->label('Organization')
                    ->placeholder('-'),
                TextEntry::make('licenseNumber')
                    ->label('License Number')
                    ->placeholder('-'),
                TextEntry::make('specialization')
                    ->label('Specialization')
                    ->formatStateUsing(fn ($state): string => match ($state) {
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
                        default => $state ?? '-',
                    })
                    ->badge()
                    ->color('info'),
                IconEntry::make('isVerified')
                    ->label('Is Verified')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
