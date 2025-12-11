<?php

namespace App\Filament\Widgets;

use App\Models\AbortedPregnancy;
use App\Models\BirthEvent;
use App\Models\Deworming;
use App\Models\Disposal;
use App\Models\Dryoff;
use App\Models\Feeding;
use App\Models\Insemination;
use App\Models\Medication;
use App\Models\Milking;
use App\Models\Pregnancy;
use App\Models\Transfer;
use App\Models\Vaccination;
use App\Models\WeightChange;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class LivestockByTypeChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Event Logs Activity (Last 14 Days)';
    }

    protected function getData(): array
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(13);

        $period = CarbonPeriod::create($startDate, $endDate);

        // Get data for all event log types
        $logs = [
            'feedings' => Feeding::class,
            'dewormings' => Deworming::class,
            'weightChanges' => WeightChange::class,
            'vaccinations' => Vaccination::class,
            'medications' => Medication::class,
            'disposals' => Disposal::class,
            'transfers' => Transfer::class,
            'inseminations' => Insemination::class,
            'milkings' => Milking::class,
            'pregnancies' => Pregnancy::class,
            'dryoffs' => Dryoff::class,
            'birthEvents' => BirthEvent::class,
            'abortedPregnancies' => AbortedPregnancy::class,
        ];

        $data = [];
        foreach ($logs as $key => $model) {
            $data[$key] = $model::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->groupBy('date')
                ->pluck('count', 'date');
        }

        $labels = [];
        $datasets = [];

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            // Initialize data arrays on first iteration
            if (empty($datasets)) {
                foreach ($logs as $key => $model) {
                    $datasets[$key] = [];
                }
            }

            // Collect data for each log type
            foreach ($logs as $key => $model) {
                $datasets[$key][] = $data[$key][$formattedDate] ?? 0;
            }
        }

        // Define distinct colors for each dataset (ensuring maximum visual separation)
        $colors = [
            'feedings' => ['rgba(34, 197, 94, 1)', 'rgba(34, 197, 94, 0.2)'], // Lime Green
            'dewormings' => ['rgba(59, 130, 246, 1)', 'rgba(59, 130, 246, 0.2)'], // Royal Blue
            'weightChanges' => ['rgba(234, 179, 8, 1)', 'rgba(234, 179, 8, 0.2)'], // Gold Yellow
            'vaccinations' => ['rgba(139, 92, 246, 1)', 'rgba(139, 92, 246, 0.2)'], // Indigo Purple
            'medications' => ['rgba(125, 211, 252, 1)', 'rgba(125, 211, 252, 0.2)'], // Light Sky Blue
            'disposals' => ['rgba(239, 68, 68, 1)', 'rgba(239, 68, 68, 0.2)'], // Bright Red
            'transfers' => ['rgba(14, 165, 233, 1)', 'rgba(14, 165, 233, 0.2)'], // Sky Cyan
            'inseminations' => ['rgba(249, 115, 22, 1)', 'rgba(249, 115, 22, 0.2)'], // Burnt Orange
            'milkings' => ['rgba(168, 85, 247, 1)', 'rgba(168, 85, 247, 0.2)'], // Purple
            'pregnancies' => ['rgba(251, 191, 36, 1)', 'rgba(251, 191, 36, 0.2)'], // Amber Yellow
            'dryoffs' => ['rgba(107, 114, 128, 1)', 'rgba(107, 114, 128, 0.2)'], // Neutral Gray
            'birthEvents' => ['rgba(5, 150, 105, 1)', 'rgba(5, 150, 105, 0.2)'], // Emerald Green
            'abortedPregnancies' => ['rgba(30, 41, 59, 1)', 'rgba(30, 41, 59, 0.2)'], // Dark Slate
        ];

        $labelsMap = [
            'feedings' => 'Feedings',
            'dewormings' => 'Dewormings',
            'weightChanges' => 'Weight Changes',
            'vaccinations' => 'Vaccinations',
            'medications' => 'Medications',
            'disposals' => 'Disposals',
            'transfers' => 'Transfers',
            'inseminations' => 'Inseminations',
            'milkings' => 'Milkings',
            'pregnancies' => 'Pregnancies',
            'dryoffs' => 'Dryoffs',
            'birthEvents' => 'Birth Events',
            'abortedPregnancies' => 'Aborted Pregnancies',
        ];

        $chartDatasets = [];
        foreach ($logs as $key => $model) {
            $chartDatasets[] = [
                'label' => $labelsMap[$key],
                'data' => $datasets[$key],
                'borderColor' => $colors[$key][0],
                'backgroundColor' => $colors[$key][1],
                'tension' => 0.3,
            ];
        }

        return [
            'datasets' => $chartDatasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Date',
                    ],
                    'grid' => [
                        'color' => 'rgba(107, 114, 128, 0.3)', // Dark grey for dark mode
                    ],
                    'border' => [
                        'color' => 'rgba(107, 114, 128, 0.5)', // Dark grey border
                    ],
                    'ticks' => [
                        'color' => 'rgba(156, 163, 175, 1)', // Light grey for tick labels
                    ],
                ],
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Events',
                    ],
                    'grid' => [
                        'color' => 'rgba(107, 114, 128, 0.3)', // Dark grey for dark mode
                    ],
                    'border' => [
                        'color' => 'rgba(107, 114, 128, 0.5)', // Dark grey border
                    ],
                    'ticks' => [
                        'color' => 'rgba(156, 163, 175, 1)', // Light grey for tick labels
                    ],
                ],
            ],
        ];
    }
}

