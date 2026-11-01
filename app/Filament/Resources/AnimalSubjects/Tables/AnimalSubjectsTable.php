<?php

namespace App\Filament\Resources\AnimalSubjects\Tables;

use App\Filament\Resources\AnimalSubjects\AnimalSubjectResource;
use App\Models\AnimalSpecies;
use App\Models\AnimalSubject;
use App\Services\SubjectLifecycleService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AnimalSubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->selectable()
            ->toolbarActions([
                Action::make('view_all')
                    ->label('All')
                    ->badge(fn ($livewire) => (string) ($livewire ? $livewire->getActiveCount() : AnimalSubject::query()->whereNull('deleted_at')->count()))
                    ->color(fn ($livewire) => ($livewire?->recordView ?? 'active') === 'active' ? 'primary' : 'gray')
                    ->button()
                    ->outlined(fn ($livewire) => ($livewire?->recordView ?? 'active') !== 'active')
                    ->action(fn ($livewire) => $livewire?->setRecordView('active')),

                Action::make('view_archived')
                    ->label('Archived')
                    ->badge(fn ($livewire) => (string) ($livewire ? $livewire->getArchivedCount() : AnimalSubject::onlyTrashed()->count()))
                    ->badgeColor(fn ($livewire) => ($livewire?->recordView ?? 'active') === 'archived' ? 'danger' : 'gray')
                    ->color(fn ($livewire) => ($livewire?->recordView ?? 'active') === 'archived' ? 'danger' : 'gray')
                    ->button()
                    ->outlined(fn ($livewire) => ($livewire?->recordView ?? 'active') !== 'archived')
                    ->action(fn ($livewire) => $livewire?->setRecordView('archived')),
            ])
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('display_name')
                    ->label('Animal / Group')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->placeholder('Unnamed')
                    ->url(fn (AnimalSubject $record) => AnimalSubjectResource::getUrl('view', ['record' => $record->id])),
                TextColumn::make('identifiers')
                    ->label('RFID & Tags')
                    ->state(fn (AnimalSubject $record) => DB::table('subject_identifiers')
                        ->where('subject_uuid', $record->uuid)
                        ->where('status', 'active')
                        ->whereNull('deleted_at')
                        ->pluck('display_value')
                        ->toArray())
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->placeholder('—'),
                TextColumn::make('species.name')
                    ->label('Species')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('sex_code')
                    ->label('Sex')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? str($state)->headline() : '—')
                    ->color(fn ($state) => match(strtolower((string)$state)) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('birth_or_start_date')
                    ->label('Birth Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('unit_type')
                    ->label('Mode')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'group' ? 'Group' : 'Individual')
                    ->color(fn ($state) => $state === 'group' ? 'warning' : 'primary')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lifecycle_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'active' => 'Active',
                        'archived' => 'Archived',
                        default => $state ? str($state)->headline() : 'Active',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'archived' => 'danger',
                        default => 'success',
                    }),
                TextColumn::make('current_stage_code')
                    ->label('Stage')
                    ->formatStateUsing(fn ($state) => $state ? str($state)->headline() : '—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('species_uuid')
                    ->label('Species')
                    ->options(fn () => AnimalSpecies::orderBy('name')->pluck('name', 'uuid')),
                SelectFilter::make('unit_type')
                    ->label('Mode')
                    ->options([
                        'individual' => 'Individual',
                        'group' => 'Group',
                    ]),
                SelectFilter::make('lifecycle_status')
                    ->label('Status')
                    ->options(fn () => AnimalSubject::query()->distinct()->whereNotNull('lifecycle_status')->pluck('lifecycle_status', 'lifecycle_status')),
                SelectFilter::make('farm_uuid')
                    ->label('Farm')
                    ->searchable()
                    ->options(fn () => DB::table('farms')
                        ->whereIn('uuid', AnimalSubject::query()->distinct()->select('farm_uuid'))
                        ->orderBy('name')
                        ->pluck('name', 'uuid')),
                TrashedFilter::make()
                    ->visible(fn () => auth()->user()?->hasPlatformPermission('records.restore') ?? false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (AnimalSubject $record) => AnimalSubjectResource::getUrl('view', ['record' => $record->id])),
                    Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('reason')
                                ->label('Archive reason')
                                ->required()
                                ->minLength(3)
                                ->maxLength(1000),
                        ])
                        ->visible(fn (AnimalSubject $record) => ! $record->trashed() && (auth()->user()?->hasPlatformPermission('records.archive') ?? false))
                        ->action(function (AnimalSubject $record, array $data): void {
                            app(SubjectLifecycleService::class)->archive($record, auth()->user(), $data['reason']);
                            Notification::make()->title('Animal archived')->success()->send();
                        }),
                    RestoreAction::make()
                        ->visible(fn (AnimalSubject $record) => $record->trashed() && (auth()->user()?->hasPlatformPermission('records.restore') ?? false))
                        ->using(function (AnimalSubject $record): bool {
                            app(SubjectLifecycleService::class)->restore($record, auth()->user());

                            return true;
                        }),
                ]),
            ]);
    }
}
