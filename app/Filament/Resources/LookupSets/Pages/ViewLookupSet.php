<?php

namespace App\Filament\Resources\LookupSets\Pages;

use App\Filament\Resources\LookupSets\LookupSetResource;
use App\Models\LookupSet;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewLookupSet extends ViewRecord
{
    protected static string $resource = LookupSetResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lookup Set Basic Information')
                    ->description('Core list identity, versioning, and scope')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Lookup Set Name')
                            ->icon('heroicon-o-list-bullet')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('code')
                            ->label('Lookup Code')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('gray')
                            ->copyable(),
                        TextEntry::make('version')
                            ->label('Version')
                            ->badge()
                            ->icon('heroicon-o-code-bracket'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'published' => 'success',
                                'draft' => 'warning',
                                'retired' => 'gray',
                                default => 'info',
                            }),
                        TextEntry::make('is_system')
                            ->label('System Protection')
                            ->badge()
                            ->state(fn (LookupSet $record) => $record->is_system ? 'System Set' : 'Custom Set')
                            ->color(fn (string $state): string => $state === 'System Set' ? 'info' : 'gray'),
                        TextEntry::make('is_extensible')
                            ->label('Extensibility')
                            ->badge()
                            ->state(fn (LookupSet $record) => $record->is_extensible ? 'Extensible' : 'Fixed')
                            ->color(fn (string $state): string => $state === 'Extensible' ? 'success' : 'gray'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Options & Hierarchies')
                    ->description('Options defined for this dropdown dataset')
                    ->schema([
                        TextEntry::make('parent.name')
                            ->label('Parent Lookup List')
                            ->icon('heroicon-o-arrows-pointing-in')
                            ->placeholder('None (Top level dataset)'),
                        TextEntry::make('options_count')
                            ->label('Total Available Choices')
                            ->icon('heroicon-o-check-badge')
                            ->badge()
                            ->color('info')
                            ->state(fn (LookupSet $record) => $record->options()->count()),
                        TextEntry::make('options_preview')
                            ->label('Configured Choices')
                            ->columnSpanFull()
                            ->state(function (LookupSet $record) {
                                $options = $record->options()->orderBy('sort_order')->limit(20)->get();
                                if ($options->isEmpty()) {
                                    return 'No options added yet.';
                                }
                                return $options->map(function ($opt) {
                                    $label = is_array($opt->label_translations_json)
                                        ? ($opt->label_translations_json['en'] ?? reset($opt->label_translations_json))
                                        : $opt->code;
                                    return "{$opt->code} ({$label})";
                                })->implode(' · ');
                            }),
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
}
