<?php

namespace App\Http\Controllers\Filament;

use App\Reports\IncomeExpenditureReportData;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeExpenditureReportPdfController
{
    public function __invoke(Request $request)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $fromDate = $this->normalizeDate($request->query('from'));
        $toDate = $this->normalizeDate($request->query('to'));
        $farmUuid = $request->filled('farmUuid') ? (string) $request->query('farmUuid') : null;
        $farmerId = $request->filled('farmerId') ? (string) $request->query('farmerId') : null;

        $payload = IncomeExpenditureReportData::build($fromDate, $toDate, $farmUuid, $farmerId);

        $brandName = Filament::getCurrentPanel()?->getBrandName() ?? config('app.name', 'Report');

        $pdf = Pdf::loadView('pdf.income-expenditure-report', [
            'generatedAt' => now(),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'farmUuid' => $farmUuid,
            'farmerId' => $farmerId,
            'brandName' => $brandName,
            ...$payload,
        ])->setPaper('a4', 'portrait');

        $filename = 'income-expenditure-report-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }

    protected function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_string($value)) {
            return null;
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
