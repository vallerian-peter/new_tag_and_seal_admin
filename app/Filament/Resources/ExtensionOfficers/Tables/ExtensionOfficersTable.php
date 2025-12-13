<?php

namespace App\Filament\Resources\ExtensionOfficers\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExtensionOfficersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('firstName')
                    ->label('Full Name')
                    ->formatStateUsing(function ($state, $record) {
                        return trim(
                            ($record->firstName ?? '') . ' ' . 
                            ($record->middleName ?? '') . ' ' . 
                            ($record->lastName ?? '')
                        );
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->whereRaw("CONCAT(COALESCE(firstName, ''), ' ', COALESCE(middleName, ''), ' ', COALESCE(lastName, '')) LIKE ?", ["%{$search}%"]);
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderByRaw("CONCAT(COALESCE(firstName, ''), ' ', COALESCE(middleName, ''), ' ', COALESCE(lastName, '')) {$direction}");
                    })
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-phone'),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label('Country')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('region.name')
                    ->label('Region')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ward.name')
                    ->label('Ward')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('organization')
                    ->label('Organization')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('specialization')
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
                        default => $state ?? 'Not specified',
                    })
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                IconColumn::make('isVerified')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('farmInvites_count')
                    ->label('Farm Invites')
                    ->counts('farmInvites')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
