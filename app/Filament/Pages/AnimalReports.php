<?php

namespace App\Filament\Pages;

use App\Services\AnimalReportService;
use App\Services\BehaviorMapping;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use UnitEnum;

/** Reports read the summary tables and the ledger, the same sources as GET /v1/reports. */
class AnimalReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static UnitEnum|string|null $navigationGroup = 'Animals & Livestock';

    protected static ?string $navigationLabel = 'Animal Reports';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'reports/animals';

    protected string $view = 'filament.pages.animal-reports';

    public string $tab = 'animals';

    public ?string $farmUuid = null;

    public ?string $from = null;

    public ?string $to = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPlatformPermission('subjects.view') ?? false;
    }

    public function mount(): void
    {
        $this->from = now()->subDays(90)->toDateString();
        $this->to = now()->toDateString();
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['animals', 'events', 'reproduction', 'production', 'health'], true) ? $tab : 'animals';
    }

    public function getTitle(): string
    {
        return 'Animal Reports';
    }

    protected function getViewData(): array
    {
        $from = $this->validDate($this->from);
        $to = $this->validDate($this->to);
        if ($from && $to && $from > $to) {
            [$from, $to] = [$to, $from];
        }
        $scope = ['farms' => $this->farmUuid ? [$this->farmUuid] : null, 'from' => $from, 'to' => $to];
        $reports = app(AnimalReportService::class);

        return [
            'farms' => DB::table('farms')->whereIn('uuid', DB::table('animal_subjects')->distinct()->select('farm_uuid'))->orderBy('name')->pluck('name', 'uuid'),
            'families' => BehaviorMapping::FAMILIES,
            'data' => match ($this->tab) {
                'events' => $reports->events($scope),
                'reproduction' => $reports->reproduction($scope),
                'production' => $reports->production($scope),
                'health' => $reports->health($scope),
                default => $reports->animals($scope),
            },
        ];
    }

    private function validDate(?string $value): ?string
    {
        return $value && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }
}
