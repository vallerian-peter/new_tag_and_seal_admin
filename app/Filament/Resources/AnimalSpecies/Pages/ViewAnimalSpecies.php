<?php

namespace App\Filament\Resources\AnimalSpecies\Pages;

use App\Filament\Resources\AnimalSpecies\AnimalSpeciesResource;
use App\Models\AnimalSpecies;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class ViewAnimalSpecies extends ViewRecord
{
    protected static string $resource = AnimalSpeciesResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Species Basic Information')
                    ->description('Core species identity, naming, and management modes')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Species Name')
                            ->icon('heroicon-o-globe-alt')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('code')
                            ->label('Species Code')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('gray')
                            ->copyable(),
                        TextEntry::make('default_subject_mode')
                            ->label('Subject Mode')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('group_display_name')
                            ->label('Group Label')
                            ->placeholder('None'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'published' => 'success',
                                'draft' => 'warning',
                                'retired' => 'gray',
                                default => 'info',
                            }),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Capabilities & Bindings')
                    ->description('Reproductive capabilities, commercial products, and mobile app linking')
                    ->schema([
                        TextEntry::make('legacy_livestock_type_id')
                            ->label('Linked Mobile Livestock Type')
                            ->icon('heroicon-o-squares-plus')
                            ->formatStateUsing(fn ($state) => $state ? DB::table('livestock_types')->where('id', $state)->value('name') : 'Not linked')
                            ->weight('medium'),
                        TextEntry::make('capabilities_json.reproduction')
                            ->label('Reproduction Model')
                            ->icon('heroicon-o-arrow-path')
                            ->badge()
                            ->formatStateUsing(fn ($state) => AnimalSpeciesResource::REPRODUCTION[$state] ?? $state),
                        TextEntry::make('capabilities_json.products')
                            ->label('Agricultural Products')
                            ->icon('heroicon-o-shopping-bag')
                            ->state(fn (AnimalSpecies $record) => implode(', ', $record->capabilities_json['products'] ?? ['None'])),
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
