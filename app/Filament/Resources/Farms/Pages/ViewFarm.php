<?php

namespace App\Filament\Resources\Farms\Pages;

use App\Filament\Resources\Farms\FarmResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewFarm extends ViewRecord
{
    protected static string $resource = FarmResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Farm Basic Information')
                    ->description('Core farm details and identifiers')
                    ->schema([
                        TextEntry::make('uuid')
                            ->label('Farm UUID')
                            ->icon('heroicon-o-key')
                            ->copyable(),
                        TextEntry::make('referenceNo')
                            ->label('Reference Number')
                            ->icon('heroicon-o-document-text')
                            ->copyable(),
                        TextEntry::make('regionalRegNo')
                            ->label('Regional Registration No')
                            ->copyable(),
                        TextEntry::make('name')
                            ->label('Farm Name')
                            ->icon('heroicon-o-building-storefront')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('size')
                            ->label('Farm Size')
                            ->icon('heroicon-o-scale'),
                        TextEntry::make('sizeUnit')
                            ->label('Size Unit')
                            ->badge(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Farmer & Legal Information')
                    ->description('Farm ownership and legal status')
                    ->schema([
                        TextEntry::make('farmerId')
                            ->label('Farmer')
                            ->icon('heroicon-o-user')
                            ->formatStateUsing(function ($record) {
                                if (!$record->relationLoaded('farmer')) {
                                    $record->load('farmer');
                                }
                                if ($record->farmer) {
                                    return trim(($record->farmer->firstName ?? '') . ' ' . 
                                               ($record->farmer->middleName ?? '') . ' ' . 
                                               ($record->farmer->surname ?? ''));
                                }
                                return 'N/A';
                            })
                            ->weight('bold'),
                        TextEntry::make('legalStatusId')
                            ->label('Legal Status')
                            ->formatStateUsing(function ($record) {
                                if (!$record->relationLoaded('legalStatus')) {
                                    $record->load('legalStatus');
                                }
                                return $record->legalStatus?->name ?? 'N/A';
                            }),
                        TextEntry::make('status')
                            ->label('Farm Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'not-active' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(3),

                Section::make('GPS Coordinates')
                    ->description('Farm geographical position')
                    ->schema([
                        TextEntry::make('latitudes')
                            ->label('Latitude')
                            ->icon('heroicon-o-map-pin')
                            ->copyable(),
                        TextEntry::make('longitudes')
                            ->label('Longitude')
                            ->icon('heroicon-o-map-pin')
                            ->copyable(),
                        TextEntry::make('physicalAddress')
                            ->label('Physical Address')
                            ->icon('heroicon-o-map')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Location Hierarchy')
                    ->description('Administrative location details')
                    ->schema([
                        TextEntry::make('countryId')
                            ->label('Country')
                            ->formatStateUsing(function ($record) {
                                if (!$record->relationLoaded('country')) {
                                    $record->load('country');
                                }
                                return $record->country?->name ?? 'N/A';
                            })
                            ->icon('heroicon-o-globe-alt'),
                        TextEntry::make('regionId')
                            ->label('Region')
                            ->formatStateUsing(function ($record) {
                                if (!$record->relationLoaded('region')) {
                                    $record->load('region');
                                }
                                return $record->region?->name ?? 'N/A';
                            })
                            ->icon('heroicon-o-map'),
                        TextEntry::make('districtId')
                            ->label('District')
                            ->formatStateUsing(function ($record) {
                                if (!$record->relationLoaded('district')) {
                                    $record->load('district');
                                }
                                return $record->district?->name ?? 'N/A';
                            })
                            ->icon('heroicon-o-map-pin'),
                        TextEntry::make('wardId')
                            ->label('Ward')
                            ->formatStateUsing(function ($record) {
                                if (!$record->relationLoaded('ward')) {
                                    $record->load('ward');
                                }
                                return $record->ward?->name ?? 'N/A';
                            })
                            ->icon('heroicon-o-map-pin'),
                        TextEntry::make('villageId')
                            ->label('Village')
                            ->formatStateUsing(function ($record) {
                                if (!$record->relationLoaded('village')) {
                                    $record->load('village');
                                }
                                return $record->village?->name ?? 'N/A';
                            })
                            ->icon('heroicon-o-home')
                            ->placeholder('N/A'),
                    ])
                    ->columns(5)
                    ->collapsible()
                    ->collapsed(),

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

