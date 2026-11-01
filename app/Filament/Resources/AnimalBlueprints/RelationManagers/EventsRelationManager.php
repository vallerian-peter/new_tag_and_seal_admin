<?php

namespace App\Filament\Resources\AnimalBlueprints\RelationManagers;

use App\Models\EventDefinition;
use App\Services\DynamicConfigurationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $title = 'Blueprint Events';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('name')
                    ->label('Event Name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('behavior_code')
                    ->label('Behaviour')
                    ->formatStateUsing(fn ($state) => $state ? Str::headline($state) : 'None'),

                TextColumn::make('family')
                    ->label('Family')
                    ->badge()
                    ->color('info')
                    ->default(fn (EventDefinition $record) => $record->familyCode() ? Str::headline($record->familyCode()) : 'General'),

                TextColumn::make('form_version')
                    ->label('Form Version')
                    ->badge()
                    ->formatStateUsing(fn ($state) => "v{$state}"),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'retired' => 'gray',
                        default => 'info',
                    }),
            ])
            ->headerActions([
                Action::make('reuse_or_clone_event')
                    ->label('Attach / Reuse Existing Event')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('primary')
                    ->visible(fn () => $this->getOwnerRecord()->status === 'draft')
                    ->form([
                        Select::make('source_event_uuid')
                            ->label('Select Existing Event Template')
                            ->placeholder('Choose an existing event (e.g. Weight Record, Calving, Vaccination)...')
                            ->options(fn () => EventDefinition::orderBy('name')->get()->mapWithKeys(fn ($e) => [$e->uuid => "{$e->name} ({$e->code})"]))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) return;
                                $source = EventDefinition::where('uuid', $state)->first();
                                if ($source) {
                                    $set('custom_name', $source->name);
                                    $set('custom_code', $source->code);
                                }
                            }),

                        TextInput::make('custom_name')
                            ->label('Event Name for this Blueprint')
                            ->helperText('You can keep the exact existing name (e.g. Weight Record) or customize it.')
                            ->required(),

                        TextInput::make('custom_code')
                            ->label('Event Code')
                            ->regex('/^[a-z][a-z0-9_]*$/')
                            ->required(),

                        Radio::make('mode')
                            ->label('Form Setup Mode')
                            ->options([
                                'reuse' => 'Reuse Existing Form (Universal event — identical fields across animals, e.g. Weight)',
                                'clone' => 'Clone Form with Edits (Create an independent copy of fields for this species)',
                            ])
                            ->default('reuse')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $blueprint = $this->getOwnerRecord();
                        $sourceEvent = EventDefinition::where('uuid', $data['source_event_uuid'])->firstOrFail();
                        $cloneForm = ($data['mode'] === 'clone');

                        app(DynamicConfigurationService::class)->reuseOrCloneEvent(
                            $blueprint,
                            $sourceEvent,
                            $cloneForm,
                            $data['custom_name'] ?? null,
                            $data['custom_code'] ?? null,
                            auth()->user()
                        );

                        Notification::make()
                            ->title('Event attached to blueprint')
                            ->body($cloneForm ? 'Form cloned and event attached successfully.' : 'Existing event reused and attached successfully.')
                            ->success()
                            ->send();
                    }),

                CreateAction::make()
                    ->label('Create New Event from Scratch')
                    ->visible(fn () => $this->getOwnerRecord()->status === 'draft'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn () => $this->getOwnerRecord()->status === 'draft'),
                    DeleteAction::make()
                        ->label('Detach Event')
                        ->visible(fn () => $this->getOwnerRecord()->status === 'draft'),
                ]),
            ]);
    }
}
