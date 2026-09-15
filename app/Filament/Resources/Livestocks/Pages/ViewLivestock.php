<?php

namespace App\Filament\Resources\Livestocks\Pages;

use App\Filament\Resources\Livestocks\LivestockResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

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
                        TextEntry::make('farm.name')
                            ->label('Farm')
                            ->icon('heroicon-o-building-storefront')
                            ->placeholder('N/A'),
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
                        TextEntry::make('species.name')
                            ->label('Species')
                            ->icon('heroicon-o-beaker')
                            ->placeholder('N/A'),
                        TextEntry::make('breed.name')
                            ->label('Breed')
                            ->icon('heroicon-o-tag')
                            ->placeholder('N/A'),
                        TextEntry::make('livestockType.name')
                            ->label('Livestock Type')
                            ->icon('heroicon-o-squares-plus')
                            ->placeholder('N/A'),
                    ])
                    ->columns(3),

                Section::make('Parentage')
                    ->description('Mother and father livestock')
                    ->schema([
                        TextEntry::make('mother.identificationNumber')
                            ->label('Mother Identification Number')
                            ->formatStateUsing(fn ($record) => $record->mother ? ($record->mother->identificationNumber ?? $record->mother->name ?? 'Unknown') : 'Unknown')
                            ->icon('heroicon-o-user')
                            ->placeholder('Unknown'),
                        TextEntry::make('father.identificationNumber')
                            ->label('Father Identification Number')
                            ->formatStateUsing(fn ($record) => $record->father ? ($record->father->identificationNumber ?? $record->father->name ?? 'Unknown') : 'Unknown')
                            ->icon('heroicon-o-user')
                            ->placeholder('Unknown'),
                    ])
                    ->columns(2),

                Section::make('Acquisition Details')
                    ->description('How and when livestock joined the farm')
                    ->schema([
                        TextEntry::make('livestockObtainedMethod.name')
                            ->label('Acquisition Method')
                            ->icon('heroicon-o-arrow-path')
                            ->placeholder('N/A'),
                        TextEntry::make('dateFirstEnteredToFarm')
                            ->label('Date Entered Farm')
                            ->date()
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('weightAsOnRegistration')
                            ->label('Weight at Registration')
                            ->suffix(' kg')
                            ->icon('heroicon-o-scale'),
                    ])
                    ->columns(3),

                Section::make('Color Information')
                    ->description('Primary and secondary colors')
                    ->schema([
                        TextEntry::make('primaryColor')
                            ->label('Primary Color')
                            ->html()
                            ->formatStateUsing(fn (?string $state) => self::renderColorBadge($state)),
                        TextEntry::make('secondaryColor')
                            ->label('Secondary Color')
                            ->html()
                            ->formatStateUsing(fn (?string $state) => self::renderColorBadge($state)),
                    ])
                    ->columns(2),

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
                    ->columns(2),
            ]);
    }

    /**
     * Render a color badge with a small circular swatch placed behind (first) and followed forward by the color name.
     */
    public static function renderColorBadge(?string $state): HtmlString
    {
        if (blank($state)) {
            return new HtmlString('<span style="color: #9ca3af; font-style: italic; font-size: 0.875rem;">Not specified</span>');
        }

        $colorMap = [
            'red' => '#ef4444',
            'green' => '#22c55e',
            'blue' => '#3b82f6',
            'black' => '#18181b',
            'white' => '#ffffff',
            'brown' => '#78350f',
            'yellow' => '#eab308',
            'orange' => '#f97316',
            'pink' => '#ec4899',
            'gray' => '#6b7280',
            'grey' => '#6b7280',
            'purple' => '#a855f7',
            'tan' => '#d2b48c',
            'beige' => '#f5f5dc',
            'cream' => '#fef08a',
            'gold' => '#eab308',
            'silver' => '#cbd5e1',
            'mixed' => 'conic-gradient(#ef4444, #3b82f6, #22c55e, #eab308, #ef4444)',
        ];

        $key = strtolower(trim($state));
        $name = ucfirst($state);
        $bg = $colorMap[$key] ?? (preg_match('/^#[a-f0-9]{3,6}$/i', $state) ? $state : '#9ca3af');
        $isWhiteOrLight = in_array($key, ['white', 'cream', 'beige']);
        $borderColor = $isWhiteOrLight ? 'rgba(0,0,0,0.35)' : 'rgba(0,0,0,0.2)';

        return new HtmlString(
            '<span style="display: inline-flex; align-items: center; gap: 8px; padding: 4px 12px; border-radius: 9999px; background-color: rgba(156, 163, 175, 0.12); border: 1px solid rgba(156, 163, 175, 0.25); font-size: 0.875rem; font-weight: 500;">' .
                '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background: ' . $bg . '; border: 1.5px solid ' . $borderColor . '; box-shadow: 0 1px 2px rgba(0,0,0,0.15); flex-shrink: 0;"></span>' .
                '<span>' . e($name) . '</span>' .
            '</span>'
        );
    }
}

