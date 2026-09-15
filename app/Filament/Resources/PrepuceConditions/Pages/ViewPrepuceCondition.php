<?php

namespace App\Filament\Resources\PrepuceConditions\Pages;

use App\Filament\Resources\PrepuceConditions\PrepuceConditionResource;
use App\Models\ExtensionOfficer;
use App\Models\PrepuceClinicalSign;
use App\Models\PrepuceTreatmentGiven;
use App\Models\Vet;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ViewPrepuceCondition extends ViewRecord
{
    protected static string $resource = PrepuceConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->description('Livestock and event details')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('livestock.identificationNumber')
                            ->label('Livestock Tag')
                            ->icon('heroicon-o-tag')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->color('primary')
                            ->placeholder('—'),
                        TextEntry::make('farm.name')
                            ->label('Farm')
                            ->icon('heroicon-o-home')
                            ->weight(FontWeight::Medium)
                            ->placeholder('—'),
                        TextEntry::make('eventDate')
                            ->label('Event Date')
                            ->icon('heroicon-o-calendar')
                            ->formatStateUsing(fn ($record, $state) => ($state ?? $record->created_at)?->format('d M Y, H:i') ?? '—'),
                    ])
                    ->columns(3),

                Section::make('Clinical Diagnosis & Assessment')
                    ->description('Observed condition, severity, and clinical signs')
                    ->icon('heroicon-o-shield-exclamation')
                    ->schema([
                        TextEntry::make('conditionType.name')
                            ->label('Condition Type')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->weight(FontWeight::Bold)
                            ->placeholder('—'),
                        TextEntry::make('severity.name')
                            ->label('Severity')
                            ->badge()
                            ->color(fn (?string $state): string => match (strtolower((string) $state)) {
                                'mild' => 'info',
                                'moderate' => 'warning',
                                'severe' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('—'),
                        TextEntry::make('causeRisk.name')
                            ->label('Cause / Risk')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->placeholder('—'),
                        TextEntry::make('clinicalSignIds')
                            ->label('Observed Clinical Signs')
                            ->badge()
                            ->color('danger')
                            ->state(function ($record): array {
                                $ids = $record->clinicalSignIds ?? [];
                                if (blank($ids)) {
                                    return [];
                                }
                                $cleanIds = array_values(array_unique(array_map('intval', array_filter($ids, fn ($v) => $v !== null && $v !== ''))));
                                if ($cleanIds === []) {
                                    return [];
                                }
                                return PrepuceClinicalSign::query()->whereIn('id', $cleanIds)->orderBy('name')->pluck('name')->toArray();
                            })
                            ->placeholder('No clinical signs recorded')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Treatment & Medication')
                    ->description('Administered medication and veterinary provider')
                    ->icon('heroicon-o-beaker')
                    ->schema([
                        TextEntry::make('medicine.name')
                            ->label('Medicine')
                            ->icon('heroicon-o-beaker')
                            ->weight(FontWeight::Medium)
                            ->placeholder('—'),
                        TextEntry::make('administrationRoute.name')
                            ->label('Administration Route')
                            ->icon('heroicon-o-arrow-right-circle')
                            ->placeholder('—'),
                        TextEntry::make('dose')
                            ->label('Dose')
                            ->placeholder('—'),
                        TextEntry::make('quantity')
                            ->label('Quantity')
                            ->placeholder('—'),
                        TextEntry::make('attending_provider')
                            ->label('Attending Provider')
                            ->icon('heroicon-o-user')
                            ->state(function ($record): string {
                                if (!empty($record->vetId)) {
                                    $vet = Vet::query()->where('medicalLicenseNo', $record->vetId)->first();
                                    return $vet ? trim($vet->fullName . ' (' . $vet->medicalLicenseNo . ') [Vet]') : $record->vetId . ' [Vet]';
                                }
                                if (!empty($record->extensionOfficerId)) {
                                    $officer = ExtensionOfficer::query()->where('medicalLicenseNo', $record->extensionOfficerId)->first();
                                    return $officer ? trim($officer->fullName . ' (' . $officer->medicalLicenseNo . ') [Officer]') : $record->extensionOfficerId . ' [Officer]';
                                }
                                return '—';
                            })
                            ->columnSpan(2),
                        TextEntry::make('treatmentGivenIds')
                            ->label('Treatments Given')
                            ->badge()
                            ->color('success')
                            ->state(function ($record): array {
                                $ids = $record->treatmentGivenIds ?? [];
                                if (blank($ids)) {
                                    return [];
                                }
                                $cleanIds = array_values(array_unique(array_map('intval', array_filter($ids, fn ($v) => $v !== null && $v !== ''))));
                                if ($cleanIds === []) {
                                    return [];
                                }
                                return PrepuceTreatmentGiven::query()->whereIn('id', $cleanIds)->orderBy('name')->pluck('name')->toArray();
                            })
                            ->placeholder('No specific treatments recorded')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Outcome & Follow-up')
                    ->description('Breeding capability and healing progress')
                    ->icon('heroicon-o-heart')
                    ->schema([
                        TextEntry::make('healingStatus.name')
                            ->label('Healing Status')
                            ->badge()
                            ->color(fn (?string $state): string => match (strtolower((string) $state)) {
                                'healed', 'recovered' => 'success',
                                'improving', 'in treatment' => 'info',
                                'worsening', 'not healed' => 'danger',
                                default => 'warning',
                            })
                            ->placeholder('—'),
                        TextEntry::make('breedingStatus.name')
                            ->label('Breeding Status')
                            ->badge()
                            ->color(fn (?string $state): string => match (strtolower((string) $state)) {
                                'fit for breeding', 'fit', 'normal' => 'success',
                                'unfit for breeding', 'unfit', 'infertile' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('—'),
                        TextEntry::make('followUpDate')
                            ->label('Follow-up Date')
                            ->icon('heroicon-o-calendar-days')
                            ->formatStateUsing(fn ($state) => blank($state) ? '—' : Carbon::parse($state)->format('d M Y')),
                        TextEntry::make('notes')
                            ->label('Clinical Notes & Observations')
                            ->placeholder('No notes provided.')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('System Audit')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y, H:i:s'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('d M Y, H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
