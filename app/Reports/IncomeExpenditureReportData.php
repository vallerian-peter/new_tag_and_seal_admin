<?php

namespace App\Reports;

use App\Models\Bill;
use App\Models\Disposal;
use App\Models\Farm;
use App\Models\Farmer;
use App\Models\FinanceIncome;
use App\Models\FinanceExpense;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

final class IncomeExpenditureReportData
{
    /**
     * @return array{
     *     summary: array,
     *     incomeRows: list<array>,
     *     expenseRows: list<array>,
     *     billRows: list<array>,
     *     filterLabels: array{farm: string, farmer: string}
     * }
     */
    public static function build(?string $fromDate, ?string $toDate, ?string $farmUuid, ?string $farmerId): array
    {
        $financeIncomes = FinanceIncome::query()
            ->with(['farm.farmer'])
            ->when($farmUuid, fn ($q) => $q->where('farmUuid', $farmUuid))
            ->when($farmerId, fn ($q) => $q->where('farmerId', (int) $farmerId))
            ->when($fromDate || $toDate, fn ($q) => self::applyFinanceIncomeDateRange($q, $fromDate, $toDate))
            ->orderByRaw('COALESCE(incomeDate, created_at) DESC')
            ->get();

        $financeIncomeSourceUuids = $financeIncomes
            ->pluck('sourceUuid')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $disposals = Disposal::query()
            ->with(['farm.farmer'])
            ->when($farmUuid, fn ($q) => $q->where('farmUuid', $farmUuid))
            ->when($farmerId, fn ($q) => $q->whereHas('farm', fn ($farmQ) => $farmQ->where('farmerId', (int) $farmerId)))
            ->when($fromDate, fn ($q) => $q->whereDate('eventDate', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->whereDate('eventDate', '<=', $toDate))
            ->whereNotNull('salePrice')
            ->when($financeIncomeSourceUuids !== [], fn ($q) => $q->whereNotIn('uuid', $financeIncomeSourceUuids))
            ->orderByDesc('eventDate')
            ->get();

        $transfers = Transfer::query()
            ->with(['fromFarm.farmer'])
            ->when($farmUuid, fn ($q) => $q->where('farmUuid', $farmUuid))
            ->when($farmerId, fn ($q) => $q->whereHas('fromFarm', fn ($farmQ) => $farmQ->where('farmerId', (int) $farmerId)))
            ->when($fromDate, fn ($q) => $q->whereDate('eventDate', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->whereDate('eventDate', '<=', $toDate))
            ->whereNotNull('price')
            ->orderByDesc('eventDate')
            ->get();

        $expenses = FinanceExpense::query()
            ->with(['farm.farmer'])
            ->when($farmUuid, fn ($q) => $q->where('farmUuid', $farmUuid))
            ->when($farmerId, fn ($q) => $q->where('farmerId', (int) $farmerId))
            ->when($fromDate || $toDate, fn ($q) => self::applyFinanceExpenseDateRange($q, $fromDate, $toDate))
            ->orderByRaw('COALESCE(expenseDate, created_at) DESC')
            ->get();

        $bills = Bill::query()
            ->with(['farm.farmer'])
            ->when($farmUuid, fn ($q) => $q->where('farmUuid', $farmUuid))
            ->when($farmerId, fn ($q) => $q->where('farmerId', (int) $farmerId))
            ->when($fromDate, fn ($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->whereDate('created_at', '<=', $toDate))
            ->orderByDesc('created_at')
            ->get();

        $financeIncomeTotal = $financeIncomes->sum(fn (FinanceIncome $row) => (float) ($row->totalAmount ?? 0));
        $disposalIncome = $disposals->sum(fn (Disposal $row) => (float) ($row->salePrice ?? 0));
        $transferIncome = $transfers->sum(fn (Transfer $row) => (float) ($row->price ?? 0));
        $totalIncome = $financeIncomeTotal + $disposalIncome + $transferIncome;

        $billUuidsWithExpenseInRange = $expenses
            ->where('sourceType', 'bill')
            ->pluck('sourceUuid')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $orphanBillsQuery = self::orphanBillsForExpenditureQuery($billUuidsWithExpenseInRange, $fromDate, $toDate, $farmUuid, $farmerId);
        $orphanBills = (clone $orphanBillsQuery)->orderByDesc('created_at')->get();
        $orphanBillExpenditure = (float) (clone $orphanBillsQuery)->sum('amount');

        $financeExpenseTotal = $expenses->sum(fn (FinanceExpense $row) => (float) ($row->totalCost ?? 0));
        $totalExpenditure = $financeExpenseTotal + $orphanBillExpenditure;

        $summary = [
            'totalIncome' => $totalIncome,
            'totalExpenditure' => $totalExpenditure,
            'net' => $totalIncome - $totalExpenditure,
            'totalIncomeRows' => $financeIncomes->count() + $disposals->count() + $transfers->count(),
            'totalExpenseRows' => $expenses->count() + $orphanBills->count(),
        ];

        $incomeRows = [];
        foreach ($financeIncomes as $row) {
            $sourceType = (string) ($row->sourceType ?? 'income');
            $displayType = match ($sourceType) {
                'manual' => 'Manual income',
                'disposal' => 'Livestock sale',
                'transfer' => 'Transfer income',
                default => 'Income',
            };

            $incomeRows[] = [
                'type' => $displayType,
                'reference' => $displayType.' record',
                'referenceCode' => $row->referenceNo ?: $row->subjectType ?: $row->uuid,
                'date' => optional($row->incomeDate)->toDateString() ?: optional($row->created_at)->toDateString(),
                'farm' => $row->farm?->name ?? '-',
                'farmer' => trim(($row->farm?->farmer?->firstName ?? '').' '.($row->farm?->farmer?->surname ?? '')),
                'amount' => (float) ($row->totalAmount ?? 0),
                'status' => $row->status ?? 'received',
                'subject' => $row->subjectType ?? '-',
                'sourceType' => $sourceType,
            ];
        }

        foreach ($disposals as $row) {
            $incomeRows[] = [
                'type' => 'Livestock sale',
                'reference' => 'Livestock sale record',
                'referenceCode' => $row->uuid,
                'date' => $row->eventDate,
                'farm' => $row->farm?->name ?? '-',
                'farmer' => trim(($row->farm?->farmer?->firstName ?? '').' '.($row->farm?->farmer?->surname ?? '')),
                'amount' => (float) ($row->salePrice ?? 0),
                'status' => $row->status ?? 'completed',
                'sourceType' => 'disposal',
            ];
        }

        foreach ($transfers as $row) {
            $incomeRows[] = [
                'type' => 'Transfer income',
                'reference' => 'Transfer income record',
                'referenceCode' => $row->uuid,
                'date' => $row->eventDate,
                'farm' => $row->fromFarm?->name ?? '-',
                'farmer' => trim(($row->fromFarm?->farmer?->firstName ?? '').' '.($row->fromFarm?->farmer?->surname ?? '')),
                'amount' => (float) ($row->price ?? 0),
                'status' => $row->status ?? 'completed',
                'sourceType' => 'transfer',
            ];
        }

        usort($incomeRows, fn (array $a, array $b) => strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? '')));

        $rowsFromFinance = $expenses->map(function (FinanceExpense $row): array {
            $sourceType = (string) ($row->sourceType ?? 'expense');
            $displayType = match ($sourceType) {
                'manual' => 'Manual expense',
                'bill' => 'Bill-backed expense',
                default => 'Expense',
            };

            return [
                'type' => $displayType,
                'reference' => $displayType.' record',
                'referenceCode' => $row->billNo ?: $row->subjectType ?: $row->sourceUuid,
                'date' => optional($row->expenseDate)->toDateString() ?: optional($row->created_at)->toDateString(),
                'farm' => $row->farm?->name ?? '-',
                'farmer' => trim(($row->farmer?->firstName ?? '').' '.($row->farmer?->surname ?? '')),
                'amount' => (float) ($row->totalCost ?? 0),
                'status' => $row->status ?? 'pending',
                'subject' => $row->subjectType ?? '-',
                'sourceType' => $sourceType,
            ];
        });

        $rowsFromOrphanBills = $orphanBills->map(function (Bill $row): array {
            return [
                'type' => 'Bill preview',
                'reference' => 'Bill record',
                'referenceCode' => $row->billNo ?: $row->uuid,
                'date' => optional($row->created_at)->toDateString() ?? '',
                'farm' => $row->farm?->name ?? '-',
                'farmer' => trim(($row->farmer?->firstName ?? '').' '.($row->farmer?->surname ?? '')),
                'amount' => (float) ($row->amount ?? 0),
                'status' => $row->status ?? 'pending',
                'subject' => $row->subjectType ?? '-',
                'sourceType' => 'bill',
            ];
        });

        $expenseRows = $rowsFromFinance
            ->concat($rowsFromOrphanBills)
            ->sortByDesc(fn (array $r) => $r['date'] ?? '')
            ->values()
            ->all();

        $billRows = $bills->map(function (Bill $row): array {
            return [
                'billNo' => $row->billNo,
                'createdAt' => optional($row->created_at)->toDateString(),
                'farm' => $row->farm?->name ?? '-',
                'farmer' => trim(($row->farmer?->firstName ?? '').' '.($row->farmer?->surname ?? '')),
                'subjectType' => $row->subjectType ?? '-',
                'quantity' => (int) ($row->quantity ?? 1),
                'amount' => (float) ($row->amount ?? 0),
                'status' => $row->status ?? 'pending',
                'sourceType' => 'bill',
                'referenceCode' => $row->billNo ?: $row->uuid,
            ];
        })->values()->all();

        $farmer = $farmerId ? Farmer::query()->find((int) $farmerId) : null;
        $farmerLabel = $farmer
            ? trim(($farmer->firstName ?? '').' '.($farmer->surname ?? ''))
            : 'All farmers';
        if ($farmerLabel === '') {
            $farmerLabel = 'All farmers';
        }

        $filterLabels = [
            'farm' => $farmUuid ? (Farm::query()->where('uuid', $farmUuid)->value('name') ?? '—') : 'All farms',
            'farmer' => $farmerLabel,
        ];

        return compact('summary', 'incomeRows', 'expenseRows', 'billRows', 'filterLabels');
    }

    protected static function applyFinanceIncomeDateRange($query, ?string $fromDate, ?string $toDate): void
    {
        $tz = config('app.timezone');

        if ($fromDate && $toDate) {
            $start = Carbon::parse($fromDate, $tz)->startOfDay();
            $end = Carbon::parse($toDate, $tz)->endOfDay();
            $query->whereRaw(
                'COALESCE(incomeDate, created_at) >= ? AND COALESCE(incomeDate, created_at) <= ?',
                [$start, $end],
            );

            return;
        }

        if ($fromDate) {
            $start = Carbon::parse($fromDate, $tz)->startOfDay();
            $query->whereRaw('COALESCE(incomeDate, created_at) >= ?', [$start]);

            return;
        }

        if ($toDate) {
            $end = Carbon::parse($toDate, $tz)->endOfDay();
            $query->whereRaw('COALESCE(incomeDate, created_at) <= ?', [$end]);
        }
    }

    protected static function applyFinanceExpenseDateRange($query, ?string $fromDate, ?string $toDate): void
    {
        $tz = config('app.timezone');

        if ($fromDate && $toDate) {
            $start = Carbon::parse($fromDate, $tz)->startOfDay();
            $end = Carbon::parse($toDate, $tz)->endOfDay();
            $query->whereRaw(
                'COALESCE(expenseDate, created_at) >= ? AND COALESCE(expenseDate, created_at) <= ?',
                [$start, $end],
            );

            return;
        }

        if ($fromDate) {
            $start = Carbon::parse($fromDate, $tz)->startOfDay();
            $query->whereRaw('COALESCE(expenseDate, created_at) >= ?', [$start]);

            return;
        }

        if ($toDate) {
            $end = Carbon::parse($toDate, $tz)->endOfDay();
            $query->whereRaw('COALESCE(expenseDate, created_at) <= ?', [$end]);
        }
    }

    /**
     * @param  list<string>  $billUuidsWithExpenseInRange
     * @return Builder<Bill>
     */
    protected static function orphanBillsForExpenditureQuery(array $billUuidsWithExpenseInRange, ?string $fromDate, ?string $toDate, ?string $farmUuid, ?string $farmerId): Builder
    {
        $q = Bill::query()
            ->with(['farm.farmer'])
            ->when($farmUuid, fn ($query) => $query->where('farmUuid', $farmUuid))
            ->when($farmerId, fn ($query) => $query->where('farmerId', (int) $farmerId))
            ->when($fromDate, fn ($query) => $query->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('created_at', '<=', $toDate));

        if ($billUuidsWithExpenseInRange !== []) {
            $q->whereNotIn('uuid', $billUuidsWithExpenseInRange);
        }

        return $q;
    }
}
