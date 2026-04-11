<?php

namespace App\Filament\Pages;

use App\Models\Farm;
use App\Models\Farmer;
use App\Reports\IncomeExpenditureReportData;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;
use UnitEnum;

class IncomeExpenditureReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Income & Expenditure';

    protected static string|UnitEnum|null $navigationGroup = 'Bills and Report';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'reports/income-expenditure';

    protected string $view = 'filament.pages.income-expenditure-report';

    protected ?Alignment $headerActionsAlignment = Alignment::End;

    public function getTitle(): string|Htmlable
    {
        return 'Income & Expenditure Report';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Period: '.($this->fromDate ?: 'N/A').' to '.($this->toDate ?: 'N/A')
            .' · Generated: '.now()->timezone(config('app.timezone'))->format('Y-m-d H:i');
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('printReport')
                    ->label('Print report')
                    ->icon('heroicon-o-printer')
                    ->action(fn () => $this->js('window.print()')),
                Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (): string => $this->pdfDownloadUrl()),
            ])
                ->label('')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button()
                ->tooltip('Export')
                ->dropdownPlacement('bottom-end'),
        ];
    }

    protected function pdfDownloadUrl(): string
    {
        return route('filament.admin.reports.income-expenditure.pdf', [
            'from' => $this->fromDate,
            'to' => $this->toDate,
            'farmUuid' => $this->farmUuid,
            'farmerId' => $this->farmerId,
        ]);
    }

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public ?string $farmUuid = null;

    public ?string $farmerId = null;

    public array $summary = [
        'totalIncome' => 0.0,
        'totalExpenditure' => 0.0,
        'net' => 0.0,
        'totalIncomeRows' => 0,
        'totalExpenseRows' => 0,
    ];

    public array $incomeRows = [];

    public array $expenseRows = [];

    public array $billRows = [];

    /** @var array{farm: string, farmer: string} */
    public array $filterLabels = [
        'farm' => 'All farms',
        'farmer' => 'All farmers',
    ];

    public function mount(): void
    {
        $this->fromDate = now()->startOfMonth()->toDateString();
        $this->toDate = now()->toDateString();
        $this->refreshReport();
    }

    public function updatedFromDate(mixed $value): void
    {
        $this->fromDate = $this->normalizeDateInput($value);
        $this->refreshReport();
    }

    public function updatedToDate(mixed $value): void
    {
        $this->toDate = $this->normalizeDateInput($value);
        $this->refreshReport();
    }

    public function updatedFarmUuid(?string $value): void
    {
        $this->farmUuid = filled($value) ? $value : null;
        $this->refreshReport();
    }

    public function updatedFarmerId(mixed $value): void
    {
        $this->farmerId = filled($value) && (string) $value !== '' ? (string) $value : null;
        $this->refreshReport();
    }

    public function resetFilters(): void
    {
        $this->fromDate = now()->startOfMonth()->toDateString();
        $this->toDate = now()->toDateString();
        $this->farmUuid = null;
        $this->farmerId = null;
        $this->refreshReport();
    }

    public function setDatePreset(string $preset): void
    {
        $today = now()->toDateString();
        if ($preset === 'today') {
            $this->fromDate = $today;
            $this->toDate = $today;
        } elseif ($preset === 'week') {
            $this->fromDate = now()->startOfWeek()->toDateString();
            $this->toDate = $today;
        } else {
            $this->fromDate = now()->startOfMonth()->toDateString();
            $this->toDate = $today;
        }

        $this->refreshReport();
    }

    /**
     * HTML date inputs send Y-m-d strings; normalize anything else from clients.
     */
    protected function normalizeDateInput(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof CarbonInterface) {
            return $value->format('Y-m-d');
        }
        if (is_string($value)) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    public function refreshReport(): void
    {
        $data = IncomeExpenditureReportData::build(
            $this->fromDate,
            $this->toDate,
            $this->farmUuid,
            $this->farmerId,
        );

        $this->summary = $data['summary'];
        $this->incomeRows = $data['incomeRows'];
        $this->expenseRows = $data['expenseRows'];
        $this->billRows = $data['billRows'];
        $this->filterLabels = $data['filterLabels'];
    }

    public function farmOptions(): array
    {
        return Farm::query()
            ->orderBy('name')
            ->pluck('name', 'uuid')
            ->toArray();
    }

    public function farmerOptions(): array
    {
        return Farmer::query()
            ->orderBy('firstName')
            ->get(['id', 'firstName', 'surname'])
            ->mapWithKeys(function (Farmer $farmer): array {
                $label = trim(($farmer->firstName ?? '').' '.($farmer->surname ?? ''));
                return [$farmer->id => $label !== '' ? $label : (string) $farmer->id];
            })
            ->toArray();
    }
}
