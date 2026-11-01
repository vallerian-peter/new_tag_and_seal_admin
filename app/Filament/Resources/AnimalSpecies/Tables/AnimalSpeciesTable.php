<?php

namespace App\Filament\Resources\AnimalSpecies\Tables;

use App\Filament\Resources\AnimalSpecies\AnimalSpeciesResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AnimalSpeciesTable
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
                    ->label('Species Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Species Code')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('default_subject_mode')
                    ->label('Subject Mode')
                    ->badge(),
                TextColumn::make('group_display_name')
                    ->label('Group Label')
                    ->placeholder('—'),
                TextColumn::make('legacy_livestock_type_id')
                    ->label('Mobile Type')
                    ->formatStateUsing(fn ($state) => DB::table('livestock_types')->where('id', $state)->value('name'))
                    ->placeholder('Not linked'),
                TextColumn::make('capabilities_json.reproduction')
                    ->label('Reproduction')
                    ->badge()
                    ->formatStateUsing(fn ($state) => AnimalSpeciesResource::REPRODUCTION[$state] ?? $state),
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ]);
    }
}
