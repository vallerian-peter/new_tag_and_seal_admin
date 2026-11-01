<?php

namespace App\Filament\Resources\EventRecords\Tables;

use App\Filament\Actions\EditPayloadAction;
use App\Filament\Resources\AnimalSubjects\AnimalSubjectResource;
use App\Models\AnimalSpecies;
use App\Models\EventRecord;
use App\Services\BehaviorMapping;
use App\Services\DynamicEventSchemaService;
use App\Services\EventDisplay;
use App\Services\EventLifecycleService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventRecordsTable
{
    public static function configure(Table $table): Table
    {
        $schemaService = app(DynamicEventSchemaService::class);
        $eventTypes = $schemaService->getEventTypes();

        $columns = [
            TextColumn::make('#')
                ->label('#')
                ->rowIndex(),

            TextColumn::make('subject.display_name')
                ->label('Animal / Group')
                ->placeholder('Unnamed')
                ->weight('bold')
                ->searchable()
                ->url(fn (EventRecord $record) => $record->subject ? AnimalSubjectResource::getUrl('view', ['record' => $record->subject->id]) : null),

            TextColumn::make('farm.name')
                ->label('Farm')
                ->placeholder('—')
                ->searchable()
                ->sortable(),

            TextColumn::make('definition.name')
                ->label('Event Name')
                ->default(fn (EventRecord $record) => Str::headline(str_replace('record_', '', $record->event_code)))
                ->searchable(query: fn (Builder $query, string $search) => $query->where('event_code', 'like', "%$search%"))
                ->sortable()
                ->weight('medium')
                ->badge()
                ->color('info')
                ->visible(fn ($livewire) => blank($livewire?->activeTab) || $livewire?->activeTab === 'all'),

            TextColumn::make('subject.species.name')
                ->label('Species')
                ->badge()
                ->color('gray')
                ->sortable()
                ->visible(fn ($livewire) => blank($livewire?->activeTab) || $livewire?->activeTab === 'all'),

            TextColumn::make('family')
                ->label('Family')
                ->badge()
                ->color('warning')
                ->state(fn (EventRecord $record) => BehaviorMapping::FAMILIES[$record->familyCode()] ?? null)
                ->visible(fn ($livewire) => blank($livewire?->activeTab) || $livewire?->activeTab === 'all'),

            TextColumn::make('summary')
                ->label('Recorded Values')
                ->wrap()
                ->limit(120)
                ->state(fn (EventRecord $record) => collect(app(EventDisplay::class)->rows($record))->take(4)->map(fn ($row) => $row['label'].': '.$row['value'])->implode(' · '))
                ->visible(fn ($livewire) => blank($livewire?->activeTab) || $livewire?->activeTab === 'all'),
        ];

        // Dynamically add dedicated payload columns for each configured event type
        foreach ($eventTypes as $code => $info) {
            $fields = $schemaService->getFieldsForEvent($code);
            foreach ($fields as $fieldKey => $fieldMeta) {
                $columnName = "payload_{$code}_{$fieldKey}";
                $label = $fieldMeta['label'];
                $unit = $fieldMeta['unit'];

                $col = TextColumn::make($columnName)
                    ->label($label)
                    ->state(fn (EventRecord $record) => $record->payload_json[$fieldKey] ?? null)
                    ->formatStateUsing(function ($state) use ($unit) {
                        if ($state === null || $state === '') {
                            return '—';
                        }
                        if (is_bool($state)) {
                            return $state ? 'Yes' : 'No';
                        }
                        if (is_array($state)) {
                            return count($state).' items';
                        }
                        $formatted = (string) $state;
                        if ($unit) {
                            $formatted .= " {$unit}";
                        }

                        return $formatted;
                    })
                    ->visible(fn ($livewire) => $livewire?->activeTab === $code);

                // Add nice badge styling for key fields
                if (in_array($fieldKey, ['born_alive', 'weight', 'milk_quantity', 'total_eggs', 'result', 'test_result'], true)) {
                    $col->badge()->color('success')->weight('bold');
                } elseif (in_array($fieldKey, ['born_dead', 'disposal_type', 'disease', 'condition_type'], true)) {
                    $col->badge()->color('danger');
                } elseif (in_array($fieldKey, ['vaccine', 'medicine', 'product', 'session', 'service_type'], true)) {
                    $col->badge()->color('info');
                }

                $columns[] = $col;
            }
        }

        // Trailing standard columns
        $columns[] = TextColumn::make('occurred_at')
            ->label('Date Recorded')
            ->dateTime('M d, Y h:i A')
            ->sortable();

        $columns[] = TextColumn::make('record_status')
            ->label('Status')
            ->badge()
            ->formatStateUsing(fn (string $state): string => match ($state) {
                'voided' => 'Archived',
                'confirmed' => 'Active',
                'submitted' => 'Submitted',
                default => Str::headline($state),
            })
            ->color(fn (string $state): string => match ($state) {
                'voided' => 'danger',
                'confirmed' => 'success',
                'submitted' => 'info',
                default => 'gray',
            });

        $columns[] = TextColumn::make('void_reason')
            ->label('Archive Reason')
            ->placeholder('—')
            ->wrap()
            ->toggleable(isToggledHiddenByDefault: true);

        $columns[] = TextColumn::make('created_at')
            ->label('Created At')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['subject.species', 'definition', 'behavior', 'farm']))
            ->defaultSort('occurred_at', 'desc')
            ->selectable()
            ->toolbarActions([
                Action::make('view_all')
                    ->label('All')
                    ->badge(fn ($livewire) => (string) ($livewire ? $livewire->getActiveCount() : EventRecord::query()->where('record_status', '!=', 'voided')->count()))
                    ->color(fn ($livewire) => ($livewire?->recordView ?? 'active') === 'active' ? 'primary' : 'gray')
                    ->button()
                    ->outlined(fn ($livewire) => ($livewire?->recordView ?? 'active') !== 'active')
                    ->action(fn ($livewire) => $livewire?->setRecordView('active')),

                Action::make('view_archived')
                    ->label('Archived')
                    ->badge(fn ($livewire) => (string) ($livewire ? $livewire->getArchivedCount() : EventRecord::query()->where('record_status', 'voided')->count()))
                    ->badgeColor(fn ($livewire) => ($livewire?->recordView ?? 'active') === 'archived' ? 'danger' : 'gray')
                    ->color(fn ($livewire) => ($livewire?->recordView ?? 'active') === 'archived' ? 'danger' : 'gray')
                    ->button()
                    ->outlined(fn ($livewire) => ($livewire?->recordView ?? 'active') !== 'archived')
                    ->action(fn ($livewire) => $livewire?->setRecordView('archived')),
            ])
            ->columns($columns)
            ->filters([
                SelectFilter::make('event_code')
                    ->label('Event Type')
                    ->searchable()
                    ->options(fn () => EventRecord::query()
                        ->distinct()
                        ->whereNotNull('event_code')
                        ->orderBy('event_code')
                        ->pluck('event_code', 'event_code')
                        ->mapWithKeys(fn ($code) => [$code => Str::headline(str_replace('record_', '', $code))])),

                SelectFilter::make('farm_uuid')
                    ->label('Farm')
                    ->searchable()
                    ->options(fn () => DB::table('farms')->whereIn('uuid', EventRecord::query()->distinct()->select('farm_uuid'))->orderBy('name')->pluck('name', 'uuid')),

                SelectFilter::make('species')
                    ->label('Species')
                    ->options(fn () => AnimalSpecies::orderBy('name')->pluck('name', 'uuid'))
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'] ?? null, fn ($q, $species) => $q->whereIn('subject_uuid', DB::table('animal_subjects')->where('species_uuid', $species)->select('uuid')))),

                SelectFilter::make('family')
                    ->label('Family')
                    ->options(BehaviorMapping::FAMILIES)
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'] ?? null, fn ($q, $family) => $q->where(fn ($inner) => $inner->whereIn('event_definition_uuid', DB::table('event_definitions')->where('family', $family)->select('uuid'))
                        ->orWhere(fn ($fallback) => $fallback->whereIn('event_definition_uuid', DB::table('event_definitions')->whereNull('family')->select('uuid'))
                            ->whereIn('behavior_code', DB::table('behavior_definitions')->where('family', $family)->select('code')))))),

                SelectFilter::make('record_status')
                    ->label('Status')
                    ->options(['submitted' => 'Submitted', 'confirmed' => 'Confirmed', 'voided' => 'Archived']),

                Filter::make('period')
                    ->schema([DatePicker::make('from'), DatePicker::make('to')])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $from) => $q->whereDate('occurred_at', '>=', $from))
                        ->when($data['to'] ?? null, fn ($q, $to) => $q->whereDate('occurred_at', '<=', $to))),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    // ACTIVE RECORD ACTIONS: Update Payload, Archive
                    EditPayloadAction::make()
                        ->visible(fn (EventRecord $record) => $record->record_status !== 'voided'),

                    Action::make('archive')
                        ->label('Archive Record')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Event Record')
                        ->modalDescription('Archiving this event marks it as voided in the ledger and recalculates any affected subject metrics.')
                        ->form([
                            Textarea::make('reason')
                                ->label('Archive Reason')
                                ->required()
                                ->minLength(3)
                                ->maxLength(1000)
                                ->placeholder('Please state why this event is being archived / voided.'),
                        ])
                        ->visible(fn (EventRecord $record) => $record->record_status !== 'voided' && (auth()->user()?->hasPlatformPermission('records.archive') ?? auth()->user()?->hasPlatformPermission('configuration.manage') ?? true))
                        ->action(function (EventRecord $record, array $data): void {
                            app(EventLifecycleService::class)->archive($record, $data['reason'] ?? null, auth()->user());
                            Notification::make()->title('Event record archived')->success()->send();
                        }),

                    // ARCHIVED RECORD ACTIONS: Restore, Delete Permanently
                    Action::make('restore')
                        ->label('Restore Record')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Archived Event')
                        ->modalDescription('This will restore the event record back to the active ledger and recalculate animal state.')
                        ->form([
                            Textarea::make('reason')
                                ->label('Restore Reason')
                                ->placeholder('Optional reason for restoring this event record.'),
                        ])
                        ->visible(fn (EventRecord $record) => $record->record_status === 'voided' && (auth()->user()?->hasPlatformPermission('records.restore') ?? auth()->user()?->hasPlatformPermission('configuration.manage') ?? true))
                        ->action(function (EventRecord $record, array $data): void {
                            app(EventLifecycleService::class)->restore($record, $data['reason'] ?? null, auth()->user());
                            Notification::make()->title('Event record restored')->success()->send();
                        }),

                    Action::make('force_delete')
                        ->label('Delete Permanently')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Permanently Delete Event')
                        ->modalDescription('This action cannot be undone and will permanently remove this event from the ledger.')
                        ->form([
                            Textarea::make('reason')
                                ->label('Permanent Deletion Audit Reason')
                                ->required()
                                ->minLength(3)
                                ->maxLength(1000)
                                ->placeholder('Please state why this event record is being permanently deleted.'),
                        ])
                        ->visible(fn (EventRecord $record) => $record->record_status === 'voided' && (auth()->user()?->hasPlatformPermission('records.delete') ?? auth()->user()?->hasPlatformPermission('configuration.manage') ?? true))
                        ->action(function (EventRecord $record, array $data): void {
                            app(EventLifecycleService::class)->forceDelete($record, $data['reason'] ?? null, auth()->user());
                            Notification::make()->title('Event record permanently deleted')->success()->send();
                        }),

                    Action::make('details')
                        ->label('Quick Summary')
                        ->icon('heroicon-o-bars-3-bottom-left')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalHeading(fn (EventRecord $record) => $record->definition?->name ?? Str::headline(str_replace('record_', '', $record->event_code)))
                        ->modalContent(fn (EventRecord $record) => view('filament.animals.event-details', [
                            'record' => $record,
                            'rows' => app(EventDisplay::class)->rows($record),
                            'family' => BehaviorMapping::FAMILIES[$record->familyCode()] ?? null,
                        ])),
                ]),
            ]);
    }
}
