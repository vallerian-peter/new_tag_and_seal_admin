<?php

namespace App\Filament\Resources\PrepuceConditions\Tables;

use App\Models\ExtensionOfficer;
use App\Models\PrepuceClinicalSign;
use App\Models\PrepuceTreatmentGiven;
use App\Models\Vet;
use Carbon\Carbon;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrepuceConditionsTable
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
                    ->sortable(),
                TextColumn::make('livestock.identificationNumber')
                    ->label('Livestock Tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventDate')
                    ->label('Event Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('conditionType.name')
                    ->label('Condition Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('severity.name')
                    ->label('Severity')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('breedingStatus.name')
                    ->label('Breeding Status')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('healingStatus.name')
                    ->label('Healing Status')
                    ->toggleable()
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
                        ->modalHeading('Prepuce Condition Details')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalWidth('4xl')
                        ->infolist([
                            Section::make('Basic Information')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('uuid')
                                            ->label('UUID')
                                            ->weight(FontWeight::Bold)
                                            ->copyable()
                                            ->columnSpan(2),
                                        TextEntry::make('farm.name')
                                            ->label('Farm')
                                            ->default('—'),
                                        TextEntry::make('livestock.identificationNumber')
                                            ->label('Livestock Tag')
                                            ->default('—'),
                                    ]),
                                ]),
                            Section::make('Condition Assessment')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('conditionType.name')
                                            ->label('Condition Type')
                                            ->default('—'),
                                        TextEntry::make('severity.name')
                                            ->label('Severity')
                                            ->default('—'),
                                        TextEntry::make('causeRisk.name')
                                            ->label('Cause / Risk')
                                            ->default('—'),
                                        TextEntry::make('clinicalSignIds')
                                            ->label('Clinical Signs')
                                            ->state(fn ($record) => self::namesForIds($record->clinicalSignIds ?? [], PrepuceClinicalSign::class))
                                            ->columnSpan(2),
                                    ]),
                                ]),
                            Section::make('Treatment')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('treatmentGivenIds')
                                            ->label('Treatment Given')
                                            ->state(fn ($record) => self::namesForIds($record->treatmentGivenIds ?? [], PrepuceTreatmentGiven::class))
                                            ->columnSpan(2),
                                        TextEntry::make('administrationRoute.name')
                                            ->label('Administration Route')
                                            ->default('—'),
                                        TextEntry::make('medicine.name')
                                            ->label('Medicine')
                                            ->default('—'),
                                        TextEntry::make('quantity')
                                            ->label('Quantity')
                                            ->default('—'),
                                        TextEntry::make('dose')
                                            ->label('Dose')
                                            ->default('—'),
                                        TextEntry::make('vetId')
                                            ->label('Vet')
                                            ->state(fn ($record) => self::providerLabel($record->vetId, true)),
                                        TextEntry::make('extensionOfficerId')
                                            ->label('Extension Officer')
                                            ->state(fn ($record) => self::providerLabel($record->extensionOfficerId, false)),
                                    ]),
                                ]),
                            Section::make('Outcome & Follow-up')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('breedingStatus.name')
                                            ->label('Breeding Status')
                                            ->default('—'),
                                        TextEntry::make('healingStatus.name')
                                            ->label('Healing Status')
                                            ->default('—'),
                                        TextEntry::make('followUpDate')
                                            ->label('Follow-up Date')
                                            ->state(fn ($record) => blank($record->followUpDate) ? '—' : Carbon::parse($record->followUpDate)->format('d M Y'))
                                            ->columnSpan(2),
                                        TextEntry::make('notes')
                                            ->label('Notes')
                                            ->default('—')
                                            ->columnSpan(2),
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

    /**
     * @param  list<int|string>|null  $ids
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     */
    private static function namesForIds(?array $ids, string $modelClass): string
    {
        if (blank($ids)) {
            return '—';
        }

        $ids = array_values(array_unique(array_map('intval', array_filter($ids, fn ($v) => $v !== null && $v !== ''))));
        if ($ids === []) {
            return '—';
        }

        return $modelClass::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->pluck('name')
            ->implode(', ');
    }

    private static function providerLabel(?string $licenseNo, bool $isVet): string
    {
        if (blank($licenseNo)) {
            return '—';
        }

        $provider = $isVet
            ? Vet::query()->where('medicalLicenseNo', $licenseNo)->first()
            : ExtensionOfficer::query()->where('medicalLicenseNo', $licenseNo)->first();

        return $provider ? trim($provider->fullName . ' (' . $provider->medicalLicenseNo . ')') : $licenseNo;
    }
}

