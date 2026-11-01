<?php

namespace App\Filament\Resources\EventDefinitions\Pages;

use App\Filament\Resources\EventDefinitions\EventDefinitionResource;
use App\Models\EventDefinition;
use App\Services\BehaviorMapping;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewEventDefinition extends ViewRecord
{
    protected static string $resource = EventDefinitionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Basic Information')
                    ->description('Core dynamic event identifiers and status')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Event Name')
                            ->icon('heroicon-o-bolt')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('code')
                            ->label('Event Code')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('gray')
                            ->copyable(),
                        TextEntry::make('blueprint.code')
                            ->label('Species Blueprint')
                            ->icon('heroicon-o-swatch')
                            ->badge()
                            ->color('info')
                            ->placeholder('Global / Unassigned'),
                        TextEntry::make('status')
                            ->label('Event Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'published' => 'success',
                                'draft' => 'warning',
                                'retired' => 'gray',
                                default => 'info',
                            }),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                Section::make('Behaviour & Ledger Architecture')
                    ->description('Family classification, business rules engine binding, and form definition')
                    ->schema([
                        TextEntry::make('family')
                            ->label('Event Family')
                            ->icon('heroicon-o-tag')
                            ->badge()
                            ->color('info')
                            ->state(fn (EventDefinition $record) => BehaviorMapping::FAMILIES[$record->family ?? $record->behavior?->family] ?? 'General'),
                        TextEntry::make('behavior.name')
                            ->label('Behaviour Engine Rule')
                            ->icon('heroicon-o-cpu-chip')
                            ->state(fn (EventDefinition $record) => $record->behavior?->name ?? ($record->behavior_code ? str($record->behavior_code)->headline()->toString() : 'None (Ledger only)'))
                            ->weight('medium'),
                        TextEntry::make('form.code')
                            ->label('Assigned Dynamic Form')
                            ->icon('heroicon-o-document-text')
                            ->badge()
                            ->color('warning')
                            ->placeholder('No form assigned'),
                    ])
                    ->columns(3),

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
