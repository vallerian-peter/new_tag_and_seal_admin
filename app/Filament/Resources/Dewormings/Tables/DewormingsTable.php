<?php

namespace App\Filament\Resources\Dewormings\Tables;

use App\Models\ExtensionOfficer;
use App\Models\Vet;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;

class DewormingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('eventDate', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock Tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventDate')
                    ->label('Event Date')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($record, $state) => $state ?? $record->created_at),
                TextColumn::make('administrationRoute.name')
                    ->label('Administration Route')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('medicine.name')
                    ->label('Medicine')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vetId')
                    ->label('Vet License')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        if (blank($state)) {
                            return '—';
                        }

                        $vet = Vet::where('medicalLicenseNo', $state)->first();

                        return $vet
                            ? trim($vet->fullName . ' (' . $vet->medicalLicenseNo . ')')
                            : $state;
                    }),
                TextColumn::make('extensionOfficerId')
                    ->label('Extension Officer License')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        if (blank($state)) {
                            return '—';
                        }

                        $officer = ExtensionOfficer::where('medicalLicenseNo', $state)->first();

                        return $officer
                            ? trim($officer->fullName . ' (' . $officer->medicalLicenseNo . ')')
                            : $state;
                    }),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('dose')
                    ->label('Dose')
                    ->toggleable(),
                TextColumn::make('nextAdministrationDate')
                    ->label('Next Administration')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Deworming Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('2xl')
                        ->infolist([
                            Section::make('Basic Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('uuid')
                                                ->label('UUID')
                                                ->weight(FontWeight::Bold)
                                                ->copyable()
                                                ->columnSpan(2),
                                            TextEntry::make('farm.name')
                                                ->label('Farm')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-home'),
                                            TextEntry::make('livestock.identificationNumber')
                                                ->label('Livestock Tag')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-tag'),
                                        ]),
                                ]),
                            Section::make('Deworming Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('administrationRoute.name')
                                                ->label('Administration Route')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-arrow-path')
                                                ->color('info'),
                                            TextEntry::make('medicine.name')
                                                ->label('Medicine')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-beaker')
                                                ->color('success'),
                                            TextEntry::make('quantity')
                                                ->label('Quantity')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-scale')
                                                ->default('—'),
                                            TextEntry::make('dose')
                                                ->label('Dose')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-calculator')
                                                ->default('—'),
                                            TextEntry::make('nextAdministrationDate')
                                                ->label('Next Administration Date')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-calendar')
                                                ->formatStateUsing(fn ($state) => blank($state) ? '—' : Carbon::parse($state)->format('d M Y'))
                                                ->columnSpan(2),
                                        ]),
                                ]),
                            Section::make('Personnel Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('vetId')
                                                ->label('Vet License')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-user')
                                                ->state(function ($record) {
                                                    $state = $record->vetId;

                                                    if (blank($state)) {
                                                        return '—';
                                                    }

                                                    $vet = Vet::where('medicalLicenseNo', $state)->first();

                                                    return $vet
                                                        ? trim($vet->fullName . ' (' . $vet->medicalLicenseNo . ')')
                                                        : $state;
                                                }),
                                            TextEntry::make('extensionOfficerId')
                                                ->label('Extension Officer License')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-user')
                                                ->state(function ($record) {
                                                    $state = $record->extensionOfficerId;

                                                    if (blank($state)) {
                                                        return '—';
                                                    }

                                                    $officer = ExtensionOfficer::where('medicalLicenseNo', $state)->first();

                                                    return $officer
                                                        ? trim($officer->fullName . ' (' . $officer->medicalLicenseNo . ')')
                                                        : $state;
                                                }),
                                        ]),
                                ]),
                            Section::make('Date Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('eventDate')
                                                ->label('Event Date')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-calendar')
                                                ->formatStateUsing(fn ($record, $state) => blank($state) && blank($record->created_at) 
                                                    ? '—' 
                                                    : Carbon::parse($state ?? $record->created_at)->format('d M Y, H:i')),
                                            TextEntry::make('created_at')
                                                ->label('Recorded At')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-clock')
                                                ->formatStateUsing(fn ($state) => blank($state) ? '—' : Carbon::parse($state)->format('d M Y, H:i')),
                                        ]),
                                ]),
                        ]),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}


