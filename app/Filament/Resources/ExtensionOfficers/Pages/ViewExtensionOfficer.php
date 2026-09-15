<?php

namespace App\Filament\Resources\ExtensionOfficers\Pages;

use App\Filament\Resources\ExtensionOfficers\ExtensionOfficerResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewExtensionOfficer extends ViewRecord
{
    protected static string $resource = ExtensionOfficerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Details')
                    ->description('Extension officer personal and contact information')
                    ->schema([
                        TextEntry::make('firstName')
                            ->label('First Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('middleName')
                            ->label('Middle Name')
                            ->placeholder('N/A'),
                        TextEntry::make('lastName')
                            ->label('Last Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('gender')
                            ->label('Gender')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'male' => 'info',
                                'female' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-o-phone')
                            ->copyable(),
                    ])
                    ->columns(3),

                Section::make('Professional Details')
                    ->description('Professional qualifications and affiliations')
                    ->schema([
                        TextEntry::make('licenseNumber')
                            ->label('License Number')
                            ->icon('heroicon-o-identification')
                            ->copyable()
                            ->placeholder('N/A'),
                        TextEntry::make('organization')
                            ->label('Organization')
                            ->icon('heroicon-o-building-office')
                            ->placeholder('Independent'),
                        TextEntry::make('specialization')
                            ->label('Specialization')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => $state ? ucwords(str_replace('_', ' ', $state)) : 'General')
                            ->placeholder('General'),
                        IconEntry::make('isVerified')
                            ->label('Verified')
                            ->boolean(),
                    ])
                    ->columns(4),

                Section::make('Location & Address')
                    ->description('Geographical coverage and base address')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Physical Address')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('N/A')
                            ->columnSpanFull(),
                        TextEntry::make('country.name')
                            ->label('Country')
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('N/A'),
                        TextEntry::make('region.name')
                            ->label('Region')
                            ->icon('heroicon-o-map')
                            ->placeholder('N/A'),
                        TextEntry::make('district.name')
                            ->label('District')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('N/A'),
                        TextEntry::make('ward.name')
                            ->label('Ward')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('N/A'),
                    ])
                    ->columns(4),

                Section::make('System Information')
                    ->description('Record creation and modification audit timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(2),
            ]);
    }
}

