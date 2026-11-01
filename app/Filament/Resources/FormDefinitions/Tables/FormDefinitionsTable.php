<?php

namespace App\Filament\Resources\FormDefinitions\Tables;

use App\Filament\Resources\FormDefinitions\FormDefinitionResource;
use App\Models\FormDefinition;
use App\Services\DynamicConfigurationService;
use App\Services\FormLocalization;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FormDefinitionsTable
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
                TextColumn::make('code')
                    ->label('Form Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('version')
                    ->label('Version')
                    ->sortable()
                    ->badge(),
                TextColumn::make('purpose')
                    ->label('Purpose')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'registration' => 'info',
                        'event' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('translation_progress')
                    ->label('Translations')
                    ->badge()
                    ->color('info')
                    ->state(fn (FormDefinition $record): string => app(FormLocalization::class)->summary($record->schema_json)),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'retired' => 'gray',
                        default => 'info',
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
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'retired' => 'Retired']),
                SelectFilter::make('purpose')
                    ->label('Purpose')
                    ->options(['registration' => 'Registration', 'event' => 'Event']),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('preview')
                        ->label('Preview Form')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn (FormDefinition $record) => FormDefinitionResource::getUrl('preview', ['record' => $record])),
                    Action::make('clone')
                        ->label('Clone Form')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->visible(fn () => auth()->user()?->hasPlatformPermission('configuration.manage') ?? false)
                        ->form([
                            TextInput::make('code')
                                ->label('New form code')
                                ->required()
                                ->regex('/^[a-z][a-z0-9_.]*$/')
                                ->default(fn (FormDefinition $record) => $record->code . '_copy')
                                ->helperText('Unique code for the cloned form. Version starts at 1 in draft.'),
                        ])
                        ->action(function (FormDefinition $record, array $data): void {
                            $cloned = app(DynamicConfigurationService::class)->cloneForm($record, auth()->user(), $data['code']);
                            \Filament\Notifications\Notification::make()
                                ->title('Form cloned successfully')
                                ->body("Form {$cloned->code} created as draft v{$cloned->version}.")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
