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
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('size')
                            ->label('Farm Size')
                            ->icon('heroicon-o-scale'),
                        TextEntry::make('sizeUnit')
                            ->label('Size Unit')
                            ->badge(),
                    ])
                    ->columns(3),

                Section::make('Farmer & Legal Information')
                    ->description('Farm ownership and legal status')
                    ->schema([
                        TextEntry::make('farmerId')
                            ->label('Farmer ID')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('legalStatusId')
                            ->label('Legal Status'),
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

