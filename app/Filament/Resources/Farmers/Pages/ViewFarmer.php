<?php

namespace App\Filament\Resources\Farmers\Pages;

use App\Filament\Resources\Farmers\FarmerResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewFarmer extends ViewRecord
{
    protected static string $resource = FarmerResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('Farmer basic details and identity')
                    ->schema([
                        TextEntry::make('farmerNo')
                            ->label('Farmer Number')
                            ->icon('heroicon-o-identification')
                            ->copyable(),
                        TextEntry::make('firstName')
                            ->label('First Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('middleName')
                            ->label('Middle Name')
                            ->placeholder('N/A'),
                        TextEntry::make('surname')
                            ->label('Surname')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('dateOfBirth')
                            ->label('Date of Birth')
                            ->date()
                            ->icon('heroicon-o-cake'),
                        TextEntry::make('gender')
                            ->label('Gender')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'male' => 'info',
                                'female' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(3),

                Section::make('Contact Information')
                    ->description('Phone numbers and email address')
                    ->schema([
                        TextEntry::make('phone1')
                            ->label('Primary Phone')
                            ->icon('heroicon-o-phone')
                            ->copyable(),
                        TextEntry::make('phone2')
                            ->label('Secondary Phone')
                            ->icon('heroicon-o-device-phone-mobile')
                            ->copyable()
                            ->placeholder('N/A'),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->placeholder('N/A'),
                        TextEntry::make('physicalAddress')
                            ->label('Physical Address')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('N/A')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Identity & Education')
                    ->description('ID card and school level details')
                    ->schema([
                        TextEntry::make('identityCardTypeId')
                            ->label('ID Card Type')
                            ->placeholder('N/A'),
                        TextEntry::make('identityNumber')
                            ->label('ID Number')
                            ->copyable()
                            ->placeholder('N/A'),
                        TextEntry::make('schoolLevelId')
                            ->label('Education Level')
                            ->placeholder('N/A'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Farmer Details')
                    ->description('Type and organization membership')
                    ->schema([
                        TextEntry::make('farmerType')
                            ->label('Farmer Type')
                            ->badge(),
                        TextEntry::make('farmerOrganizationMembership')
                            ->label('Organization Membership')
                            ->placeholder('None')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Location Details')
                    ->description('Geographical location hierarchy')
                    ->schema([
                        TextEntry::make('countryId')
                            ->label('Country ID'),
                        TextEntry::make('regionId')
                            ->label('Region ID'),
                        TextEntry::make('districtId')
                            ->label('District ID'),
                        TextEntry::make('wardId')
                            ->label('Ward ID'),
                        TextEntry::make('villageId')
                            ->label('Village ID')
                            ->placeholder('N/A'),
                        TextEntry::make('streetId')
                            ->label('Street ID')
                            ->placeholder('N/A'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),

                Section::make('System Information')
                    ->description('Status and audit trail')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Farmer Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('createdBy')
                            ->label('Created By User ID')
                            ->placeholder('System'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(4)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

