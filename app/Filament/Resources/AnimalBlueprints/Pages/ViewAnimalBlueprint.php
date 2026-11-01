<?php

namespace App\Filament\Resources\AnimalBlueprints\Pages;

use App\Filament\Resources\AnimalBlueprints\AnimalBlueprintResource;
use App\Models\AnimalBlueprint;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class ViewAnimalBlueprint extends ViewRecord
{
    protected static string $resource = AnimalBlueprintResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Blueprint Identity')
                    ->description('Core species blueprint identifiers and publishing status')
                    ->schema([
                        TextEntry::make('species.name')
                            ->label('Species Name')
                            ->icon('heroicon-o-swatch')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('code')
                            ->label('Blueprint Code')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('gray')
                            ->copyable(),
                        TextEntry::make('version')
                            ->label('Blueprint Version')
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
                        TextEntry::make('registration_form_code')
                            ->label('Registration Form')
                            ->icon('heroicon-o-document-text')
                            ->badge()
                            ->color('info')
                            ->placeholder('Not assigned'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Mobile & Reproduction Configuration')
                    ->description('Animal type bindings and reproductive lifecycle tracking')
                    ->schema([
                        TextEntry::make('mobile_type')
                            ->label('Mobile Livestock Type')
                            ->icon('heroicon-o-squares-plus')
                            ->state(function (AnimalBlueprint $record) {
                                $typeId = $record->settings_json['catalogBinding']['livestockTypeId'] ?? $record->species?->legacy_livestock_type_id;
                                return $typeId ? DB::table('livestock_types')->where('id', $typeId)->value('name') : 'Configured';
                            })
                            ->weight('bold'),
                        TextEntry::make('reproduction_mode')
                            ->label('Reproduction Mode')
                            ->icon('heroicon-o-arrow-path')
                            ->state(fn (AnimalBlueprint $record) => $record->settings_json['reproduction']['mode'] ?? $record->species?->capabilities_json['reproduction'] ?? 'none')
                            ->badge(),
                        TextEntry::make('allowed_sex_codes')
                            ->label('Allowed Sex Codes')
                            ->icon('heroicon-o-user-group')
                            ->state(fn (AnimalBlueprint $record) => implode(', ', $record->settings_json['subject']['allowedSexCodes'] ?? ['M', 'F', 'C', 'UNKNOWN']))
                            ->badge(),
                    ])
                    ->columns(3),

                Section::make('Identification & Tagging Policies')
                    ->description('Rules for physical tags, RFID chips, and un-tagged subjects')
                    ->schema([
                        TextEntry::make('allowed_tag_types')
                            ->label('Allowed Tag Types')
                            ->icon('heroicon-o-tag')
                            ->state(fn (AnimalBlueprint $record) => implode(', ', $record->settings_json['identification']['allowedTypes'] ?? ['ear_tag', 'rfid', 'visual_tag'])),
                        TextEntry::make('required_tag_types')
                            ->label('Required Tag Types')
                            ->icon('heroicon-o-check-circle')
                            ->state(fn (AnimalBlueprint $record) => implode(', ', $record->settings_json['identification']['requiredTypes'] ?? ['None'])),
                        TextEntry::make('allow_without_tag')
                            ->label('Allow Registration Without Tag')
                            ->badge()
                            ->state(fn (AnimalBlueprint $record) => ($record->settings_json['identification']['allowWithoutTag'] ?? false) ? 'Allowed' : 'Requires Tag')
                            ->color(fn (string $state): string => $state === 'Allowed' ? 'warning' : 'success'),
                    ])
                    ->columns(3),

                Section::make('System Timestamps')
                    ->description('Record creation, publication, and modification dates')
                    ->schema([
                        TextEntry::make('published_at')
                            ->label('Published At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-check-badge')
                            ->placeholder('Not published yet'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(3),
            ]);
    }
}
