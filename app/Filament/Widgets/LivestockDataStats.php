<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventRecords\EventRecordResource;
use App\Models\AbortedPregnancy;
use App\Models\BirthEvent;
use App\Models\Deworming;
use App\Models\Disposal;
use App\Models\Dryoff;
use App\Models\Feeding;
use App\Models\Insemination;
use App\Models\Treatment;
use App\Models\Milking;
use App\Models\Pregnancy;
use App\Models\Transfer;
use App\Models\Vaccination;
use App\Models\WeightChange;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Route;

class LivestockDataStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Feedings', $this->countOf(Feeding::class))
                ->description('Feeding logs')
                ->descriptionIcon('heroicon-o-beaker')
                ->color('success')
                ->url($this->eventUrl('growth', 'filament.admin.resources.feedings.index'))
                ->icon('heroicon-o-beaker'),

            Stat::make('Dewormings', $this->countOf(Deworming::class))
                ->description('Deworming logs')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('info')
                ->url($this->eventUrl('health', 'filament.admin.resources.dewormings.index'))
                ->icon('heroicon-o-shield-check'),

            Stat::make('Weight Changes', $this->countOf(WeightChange::class))
                ->description('Weight change logs')
                ->descriptionIcon('heroicon-o-scale')
                ->color('warning')
                ->url($this->eventUrl('growth', 'filament.admin.resources.weight-changes.index'))
                ->icon('heroicon-o-scale'),

            Stat::make('Vaccinations', $this->countOf(Vaccination::class))
                ->description('Vaccination logs')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url($this->eventUrl('health', 'filament.admin.resources.vaccinations.index'))
                ->icon('heroicon-o-check-circle'),

            Stat::make('Treatments', $this->countOf(Treatment::class))
                ->description('Treatment logs')
                ->descriptionIcon('heroicon-o-heart')
                ->color('danger')
                ->url($this->eventUrl('health', 'filament.admin.resources.medications.index'))
                ->icon('heroicon-o-heart'),

            Stat::make('Disposals', $this->countOf(Disposal::class))
                ->description('Disposal logs')
                ->descriptionIcon('heroicon-o-trash')
                ->color('danger')
                ->url($this->eventUrl('movement', 'filament.admin.resources.disposals.index'))
                ->icon('heroicon-o-trash'),

            Stat::make('Transfers', $this->countOf(Transfer::class))
                ->description('Transfer logs')
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color('info')
                ->url($this->eventUrl('movement', 'filament.admin.resources.transfers.index'))
                ->icon('heroicon-o-arrow-right-circle'),

            Stat::make('Inseminations', $this->countOf(Insemination::class))
                ->description('Insemination logs')
                ->descriptionIcon('heroicon-o-heart')
                ->color('success')
                ->url($this->eventUrl('breeding', 'filament.admin.resources.inseminations.index'))
                ->icon('heroicon-o-heart'),

            Stat::make('Milkings', $this->countOf(Milking::class))
                ->description('Milking logs')
                ->descriptionIcon('heroicon-o-beaker')
                ->color('info')
                ->url($this->eventUrl('production', 'filament.admin.resources.milkings.index'))
                ->icon('heroicon-o-beaker'),

            Stat::make('Pregnancies', $this->countOf(Pregnancy::class))
                ->description('Pregnancy logs')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('warning')
                ->url($this->eventUrl('breeding', 'filament.admin.resources.pregnancies.index'))
                ->icon('heroicon-o-sparkles'),

            Stat::make('Dryoffs', $this->countOf(Dryoff::class))
                ->description('Dry-off logs')
                ->descriptionIcon('heroicon-o-pause-circle')
                ->color('gray')
                ->url($this->eventUrl('breeding', 'filament.admin.resources.dryoffs.index'))
                ->icon('heroicon-o-pause-circle'),

            Stat::make('Birth Events', $this->countOf(BirthEvent::class))
                ->description('Birth event logs')
                ->descriptionIcon('heroicon-o-gift')
                ->color('success')
                ->url($this->eventUrl('births', 'filament.admin.resources.birth-events.index'))
                ->icon('heroicon-o-gift'),

            Stat::make('Aborted Pregnancies', $this->countOf(AbortedPregnancy::class))
                ->description('Aborted pregnancy logs')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->url($this->eventUrl('births', 'filament.admin.resources.aborted-pregnancies.index'))
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }

    protected function countOf(string $modelClass): int
    {
        try {
            return $modelClass::count();
        } catch (\Throwable) {
            return 0;
        }
    }

    protected function eventUrl(string $family, ?string $legacyRoute = null): ?string
    {
        if ($legacyRoute && Route::has($legacyRoute)) {
            return route($legacyRoute);
        }

        if (Route::has('filament.admin.resources.event-records.index') && EventRecordResource::canViewAny()) {
            return EventRecordResource::getUrl('index', [
                'tableFilters' => [
                    'family' => ['value' => $family],
                ],
            ]);
        }

        return null;
    }
}

