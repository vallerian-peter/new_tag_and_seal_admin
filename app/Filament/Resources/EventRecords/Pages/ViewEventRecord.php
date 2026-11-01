<?php

namespace App\Filament\Resources\EventRecords\Pages;

use App\Filament\Resources\AnimalSubjects\AnimalSubjectResource;
use App\Filament\Resources\EventRecords\EventRecordResource;
use App\Models\EventRecord;
use App\Services\BehaviorMapping;
use App\Services\EventDisplay;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewEventRecord extends ViewRecord
{
    protected static string $resource = EventRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \App\Filament\Actions\EditPayloadAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Overview')
                    ->description('Primary event identity, subject animal, and farm location')
                    ->schema([
                        TextEntry::make('definition.name')
                            ->label('Event Name')
                            ->icon('heroicon-o-bolt')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success')
                            ->default(fn (EventRecord $record) => str($record->event_code)->headline()->toString()),
                        TextEntry::make('subject.display_name')
                            ->label('Animal / Group')
                            ->icon('heroicon-o-cube')
                            ->weight('bold')
                            ->url(fn (EventRecord $record) => $record->subject ? AnimalSubjectResource::getUrl('view', ['record' => $record->subject->id]) : null)
                            ->placeholder('Unnamed subject'),
                        TextEntry::make('farm.name')
                            ->label('Farm')
                            ->icon('heroicon-o-building-storefront')
                            ->placeholder('Unassigned farm'),
                        TextEntry::make('occurred_at')
                            ->label('Event Date & Time')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-calendar')
                            ->weight('medium'),
                        TextEntry::make('record_status')
                            ->label('Ledger Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'confirmed' => 'success',
                                'voided' => 'danger',
                                'submitted' => 'info',
                                default => 'gray',
                            }),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Classification & Mapping')
                    ->description('Species, behaviour family, and form schema identifiers')
                    ->schema([
                        TextEntry::make('subject.species.name')
                            ->label('Species')
                            ->icon('heroicon-o-swatch')
                            ->badge()
                            ->color('info')
                            ->placeholder('N/A'),
                        TextEntry::make('family')
                            ->label('Behaviour Family')
                            ->icon('heroicon-o-tag')
                            ->badge()
                            ->color('warning')
                            ->state(fn (EventRecord $record) => BehaviorMapping::FAMILIES[$record->familyCode()] ?? 'General'),
                        TextEntry::make('event_code')
                            ->label('Event Code')
                            ->icon('heroicon-o-hashtag')
                            ->copyable(),
                        TextEntry::make('form_code')
                            ->label('Form Code')
                            ->icon('heroicon-o-document-text')
                            ->badge()
                            ->placeholder('N/A'),
                    ])
                    ->columns(4),

                Section::make('Recorded Field Values')
                    ->description('Dynamic field entries submitted from the mobile device')
                    ->schema([
                        TextEntry::make('field_values')
                            ->label('Field Data')
                            ->columnSpanFull()
                            ->state(function (EventRecord $record) {
                                $rows = app(EventDisplay::class)->rows($record);
                                if (empty($rows)) {
                                    return 'No payload field data recorded.';
                                }
                                return collect($rows)
                                    ->map(fn ($r) => "{$r['label']}: {$r['value']}")
                                    ->implode('  |  ');
                            }),
                    ]),

                Section::make('System & Audit Metadata')
                    ->description('Audit trail, device tracking, and timestamps')
                    ->schema([
                        TextEntry::make('recorded_by_device_id')
                            ->label('Device Identifier')
                            ->icon('heroicon-o-device-phone-mobile')
                            ->placeholder('N/A'),
                        TextEntry::make('revision')
                            ->label('Revision')
                            ->icon('heroicon-o-document-duplicate')
                            ->badge()
                            ->color('info')
                            ->default(1),
                        TextEntry::make('created_at')
                            ->label('Recorded in Ledger At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(4),
            ]);
    }
}
