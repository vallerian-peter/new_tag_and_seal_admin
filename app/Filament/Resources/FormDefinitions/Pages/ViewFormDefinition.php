<?php

namespace App\Filament\Resources\FormDefinitions\Pages;

use App\Filament\Resources\FormDefinitions\FormDefinitionResource;
use App\Models\FormDefinition;
use App\Services\FormLocalization;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewFormDefinition extends ViewRecord
{
    protected static string $resource = FormDefinitionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Form Identification')
                    ->description('Core form definition identifiers and publication status')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Form Code')
                            ->icon('heroicon-o-document-text')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success')
                            ->copyable(),
                        TextEntry::make('version')
                            ->label('Form Version')
                            ->badge()
                            ->icon('heroicon-o-code-bracket'),
                        TextEntry::make('purpose')
                            ->label('Form Purpose')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'registration' => 'info',
                                'event' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->label('Form Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'published' => 'success',
                                'draft' => 'warning',
                                'retired' => 'gray',
                                default => 'info',
                            }),
                        TextEntry::make('translation_summary')
                            ->label('Translations Progress')
                            ->icon('heroicon-o-language')
                            ->badge()
                            ->color('info')
                            ->state(fn (FormDefinition $record): string => app(FormLocalization::class)->summary($record->schema_json)),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Mobile Presentation Details')
                    ->description('Display headings, instructional descriptions, and submit button text on mobile app')
                    ->schema([
                        TextEntry::make('presentation_title')
                            ->label('Screen Title')
                            ->icon('heroicon-o-window')
                            ->state(fn (FormDefinition $record) => $record->schema_json['presentation']['title'] ?? str($record->code)->headline()->toString())
                            ->weight('bold'),
                        TextEntry::make('submit_label')
                            ->label('Submit Button Label')
                            ->icon('heroicon-o-check')
                            ->badge()
                            ->color('success')
                            ->state(fn (FormDefinition $record) => $record->schema_json['presentation']['submitLabel'] ?? 'Submit Record'),
                        TextEntry::make('presentation_description')
                            ->label('User Instructions')
                            ->icon('heroicon-o-information-circle')
                            ->state(fn (FormDefinition $record) => $record->schema_json['presentation']['description'] ?? 'No special instructions.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Form Schema Architecture')
                    ->description('Summary of layout sections and dynamic field components')
                    ->schema([
                        TextEntry::make('sections_count')
                            ->label('Layout Sections')
                            ->icon('heroicon-o-rectangle-stack')
                            ->badge()
                            ->state(fn (FormDefinition $record) => count($record->schema_json['sections'] ?? [])),
                        TextEntry::make('fields_count')
                            ->label('Total Input Fields')
                            ->icon('heroicon-o-list-bullet')
                            ->badge()
                            ->color('info')
                            ->state(function (FormDefinition $record) {
                                $count = 0;
                                foreach ($record->schema_json['sections'] ?? [] as $sec) {
                                    $count += count($sec['fields'] ?? []);
                                }
                                return $count;
                            }),
                        TextEntry::make('components_list')
                            ->label('Field Components In Use')
                            ->icon('heroicon-o-squares-2x2')
                            ->state(function (FormDefinition $record) {
                                $types = [];
                                foreach ($record->schema_json['sections'] ?? [] as $sec) {
                                    foreach ($sec['fields'] ?? [] as $field) {
                                        if (isset($field['component'])) {
                                            $types[$field['component']] = true;
                                        }
                                    }
                                }
                                return count($types) > 0 ? implode(', ', array_keys($types)) : 'Standard components';
                            }),
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
