<?php

namespace App\Filament\Resources\Livestocks\Tables;

use App\Filament\Resources\Livestocks\Pages\ViewLivestock;
use App\Models\Livestock;
use App\Services\LivestockLifecycleService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LivestocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->selectable()
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Livestock Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('identificationNumber')
                    ->label('ID Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('species.name')
                    ->label('Species')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('breed.name')
                    ->label('Breed')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('livestockType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('dateOfBirth')
                    ->label('DOB')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('weightAsOnRegistration')
                    ->label('Weight (kg)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('stage.name')
                    ->label('Stage')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('isIdentified')
                    ->label('Identified')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->toggleable(),
                TextColumn::make('primaryColor')
                    ->label('Primary Color')
                    ->html()
                    ->formatStateUsing(fn (?string $state) => ViewLivestock::renderColorBadge($state))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('secondaryColor')
                    ->label('Secondary Color')
                    ->html()
                    ->formatStateUsing(fn (?string $state) => ViewLivestock::renderColorBadge($state))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Archived At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('delete_reason')
                    ->label('Archive Reason')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->visible(fn (Livestock $record): bool => ! $record->trashed()),
                    EditAction::make()
                        ->visible(fn (Livestock $record): bool => ! $record->trashed()),
                    Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('delete_reason')
                                ->label('Archive reason')
                                ->required()
                                ->minLength(10)
                                ->maxLength(1000),
                        ])
                        ->action(function (Livestock $record, array $data): void {
                            app(LivestockLifecycleService::class)->archive(
                                $record,
                                auth()->user(),
                                $data['delete_reason'],
                                [
                                    'ip_address' => request()->ip(),
                                    'user_agent' => request()->userAgent(),
                                ],
                            );

                            Notification::make()
                                ->title('Livestock archived')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Livestock $record): bool => ! $record->trashed()),
                    RestoreAction::make()
                        ->using(function (Livestock $record): bool {
                            app(LivestockLifecycleService::class)->restore(
                                $record,
                                auth()->user(),
                                [
                                    'ip_address' => request()->ip(),
                                    'user_agent' => request()->userAgent(),
                                ],
                            );

                            return true;
                        }),
                ]),
            ]);
    }
}
