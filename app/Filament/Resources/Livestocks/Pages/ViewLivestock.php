<?php

namespace App\Filament\Resources\Livestocks\Pages;

use App\Filament\Resources\Livestocks\LivestockResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewLivestock extends ViewRecord
{
    protected static string $resource = LivestockResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Livestock Identification')
                    ->description('Tags and identification numbers')
                    ->schema([
                        TextEntry::make('uuid')
                            ->label('Livestock UUID')
                            ->icon('heroicon-o-key')
                            ->copyable(),
                        TextEntry::make('identificationNumber')
                            ->label('Identification Number')
                            ->icon('heroicon-o-hashtag')
                            ->copyable(),
                        TextEntry::make('dummyTagId')
                            ->label('Dummy Tag ID')
                            ->copyable(),
                        TextEntry::make('barcodeTagId')
                            ->label('Barcode Tag ID')
                            ->copyable(),
                        TextEntry::make('rfidTagId')
                            ->label('RFID Tag ID')
                            ->icon('heroicon-o-radio')
                            ->copyable(),
                    ])
                    ->columns(3),

                Section::make('Basic Information')
                    ->description('Core livestock details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Animal Name')
                            ->icon('heroicon-o-cube')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('farmUuid')
                            ->label('Farm UUID')
                            ->icon('heroicon-o-building-storefront')
                            ->copyable(),
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
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'sold' => 'warning',
                                'dead' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(3),

                Section::make('Classification')
                    ->description('Species, breed, and type')
                    ->schema([
                        TextEntry::make('speciesId')
                            ->label('Species ID')
                            ->icon('heroicon-o-beaker'),
                        TextEntry::make('breedId')
                            ->label('Breed ID')
                            ->icon('heroicon-o-tag'),
                        TextEntry::make('livestockTypeId')
                            ->label('Livestock Type ID')
                            ->icon('heroicon-o-squares-plus'),
                    ])
                    ->columns(3),

                Section::make('Parentage')
                    ->description('Mother and father livestock')
                    ->schema([
                        TextEntry::make('motherUuid')
                            ->label('Mother UUID')
                            ->icon('heroicon-o-user')
                            ->copyable()
                            ->placeholder('Unknown'),
                        TextEntry::make('fatherUuid')
                            ->label('Father UUID')
                            ->icon('heroicon-o-user')
                            ->copyable()
                            ->placeholder('Unknown'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Acquisition Details')
                    ->description('How and when livestock joined the farm')
                    ->schema([
                        TextEntry::make('livestockObtainedMethodId')
                            ->label('Acquisition Method ID')
                            ->icon('heroicon-o-arrow-path'),
                        TextEntry::make('dateFirstEnteredToFarm')
                            ->label('Date Entered Farm')
                            ->date()
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('weightAsOnRegistration')
                            ->label('Weight at Registration')
                            ->suffix(' kg')
                            ->icon('heroicon-o-scale'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('System Timestamps')
                    ->description('Record creation and modification dates')
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
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

