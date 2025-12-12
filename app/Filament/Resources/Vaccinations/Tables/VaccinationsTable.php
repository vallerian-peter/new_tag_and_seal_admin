<?php

namespace App\Filament\Resources\Vaccinations\Tables;

use App\Models\ExtensionOfficer;
use App\Models\Vet;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VaccinationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('vaccinationNo')
                    ->label('Vaccination No.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock Tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vaccine.name')
                    ->label('Vaccine')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('disease.name')
                    ->label('Disease')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Vaccination Details')
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
                                            TextEntry::make('vaccinationNo')
                                                ->label('Vaccination No.')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-hashtag')
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
                            Section::make('Vaccination Details')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('vaccine.name')
                                                ->label('Vaccine')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-shield-check')
                                                ->color('success'),
                                            TextEntry::make('disease.name')
                                                ->label('Disease')
                                                ->weight(FontWeight::Bold)
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->color('warning')
                                                ->default('—'),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->weight(FontWeight::Bold)
                                                ->color(fn (string $state): string => match ($state) {
                                                    'pending' => 'warning',
                                                    'completed' => 'success',
                                                    'failed' => 'danger',
                                                    default => 'gray',
                                                }),
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
                            Section::make('Timestamps')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('created_at')
                                                ->label('Created At')
                                                ->dateTime()
                                                ->icon('heroicon-o-calendar'),
                                            TextEntry::make('updated_at')
                                                ->label('Updated At')
                                                ->dateTime()
                                                ->icon('heroicon-o-calendar-days'),
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

