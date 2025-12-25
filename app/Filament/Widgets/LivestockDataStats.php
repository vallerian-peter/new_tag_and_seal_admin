<?php

namespace App\Filament\Widgets;

use App\Models\AbortedPregnancy;
use App\Models\BirthEvent;
use App\Models\Breed;
use App\Models\Deworming;
use App\Models\Disposal;
use App\Models\Dryoff;
use App\Models\Farm;
use App\Models\Feeding;
use App\Models\Insemination;
use App\Models\Livestock;
use App\Models\LivestockType;
use App\Models\Treatment;
use App\Models\Milking;
use App\Models\Pregnancy;
use App\Models\Specie;
use App\Models\Transfer;
use App\Models\Vaccination;
use App\Models\WeightChange;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LivestockDataStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Farms', Farm::count())
                ->description('Registered farms in the system')
                ->descriptionIcon('heroicon-o-building-storefront')
                ->color('success')
                ->url(route('filament.admin.resources.farms.index'))
                ->icon('heroicon-o-building-storefront'),

            Stat::make('Livestock', Livestock::count())
                ->description('All livestock records')
                ->descriptionIcon('heroicon-o-cube')
                ->color('info')
                ->url(route('filament.admin.resources.livestocks.index'))
                ->icon('heroicon-o-cube'),

            Stat::make('Breeds', Breed::count())
                ->description('Registered livestock breeds')
                ->descriptionIcon('heroicon-o-tag')
                ->color('warning')
                ->url(route('filament.admin.resources.breeds.index'))
                ->icon('heroicon-o-tag'),

            Stat::make('Species', Specie::count())
                ->description('Livestock species types')
                ->descriptionIcon('heroicon-o-beaker')
                ->color('danger')
                ->url(route('filament.admin.resources.species.index'))
                ->icon('heroicon-o-beaker'),

            Stat::make('Livestock Types', LivestockType::count())
                ->description('Types of livestock')
                ->descriptionIcon('heroicon-o-squares-plus')
                ->color('info')
                ->url(route('filament.admin.resources.livestock-types.index'))
                ->icon('heroicon-o-squares-plus'),

            Stat::make('Feedings', Feeding::count())
                ->description('Feeding logs')
                ->descriptionIcon('heroicon-o-beaker')
                ->color('success')
                ->url(route('filament.admin.resources.feedings.index'))
                ->icon('heroicon-o-beaker'),

            Stat::make('Dewormings', Deworming::count())
                ->description('Deworming logs')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('info')
                ->url(route('filament.admin.resources.dewormings.index'))
                ->icon('heroicon-o-shield-check'),

            Stat::make('Weight Changes', WeightChange::count())
                ->description('Weight change logs')
                ->descriptionIcon('heroicon-o-scale')
                ->color('warning')
                ->url(route('filament.admin.resources.weight-changes.index'))
                ->icon('heroicon-o-scale'),

            Stat::make('Vaccinations', Vaccination::count())
                ->description('Vaccination logs')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.vaccinations.index'))
                ->icon('heroicon-o-check-circle'),

            Stat::make('Treatments', Treatment::count())
                ->description('Treatment logs')
                ->descriptionIcon('heroicon-o-heart')
                ->color('danger')
                ->url(route('filament.admin.resources.medications.index'))
                ->icon('heroicon-o-heart'),

            Stat::make('Disposals', Disposal::count())
                ->description('Disposal logs')
                ->descriptionIcon('heroicon-o-trash')
                ->color('danger')
                ->url(route('filament.admin.resources.disposals.index'))
                ->icon('heroicon-o-trash'),

            Stat::make('Transfers', Transfer::count())
                ->description('Transfer logs')
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color('info')
                ->url(route('filament.admin.resources.transfers.index'))
                ->icon('heroicon-o-arrow-right-circle'),

            Stat::make('Inseminations', Insemination::count())
                ->description('Insemination logs')
                ->descriptionIcon('heroicon-o-heart')
                ->color('success')
                ->url(route('filament.admin.resources.inseminations.index'))
                ->icon('heroicon-o-heart'),

            Stat::make('Milkings', Milking::count())
                ->description('Milking logs')
                ->descriptionIcon('heroicon-o-beaker')
                ->color('info')
                ->url(route('filament.admin.resources.milkings.index'))
                ->icon('heroicon-o-beaker'),

            Stat::make('Pregnancies', Pregnancy::count())
                ->description('Pregnancy logs')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('warning')
                ->url(route('filament.admin.resources.pregnancies.index'))
                ->icon('heroicon-o-sparkles'),

            Stat::make('Dryoffs', Dryoff::count())
                ->description('Dry-off logs')
                ->descriptionIcon('heroicon-o-pause-circle')
                ->color('gray')
                ->url(route('filament.admin.resources.dryoffs.index'))
                ->icon('heroicon-o-pause-circle'),

            Stat::make('Birth Events', BirthEvent::count())
                ->description('Birth event logs')
                ->descriptionIcon('heroicon-o-gift')
                ->color('success')
                ->url(route('filament.admin.resources.birth-events.index'))
                ->icon('heroicon-o-gift'),

            Stat::make('Aborted Pregnancies', AbortedPregnancy::count())
                ->description('Aborted pregnancy logs')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->url(route('filament.admin.resources.aborted-pregnancies.index'))
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}

