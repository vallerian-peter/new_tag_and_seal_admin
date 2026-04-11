{{-- Shared body markup: PDF download and browser print (must match). Expects: brandName, fromDate, toDate, filterLabels, generatedAt, summary, incomeRows, expenseRows, billRows --}}
<div class="doc-header">
    <h1 class="doc-title">Income &amp; Expenditure Report</h1>
    <table class="doc-meta" cellspacing="0">
        <tr>
            <td><strong>Organisation</strong></td>
            <td>{{ $brandName }}</td>
        </tr>
        <tr>
            <td><strong>Period</strong></td>
            <td>{{ $fromDate ?? '—' }} to {{ $toDate ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>Farm</strong></td>
            <td>{{ $filterLabels['farm'] ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>Farmer</strong></td>
            <td>{{ $filterLabels['farmer'] ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>Generated</strong></td>
            <td>{{ $generatedAt->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2 class="section-title">Summary</h2>
    <table class="kpi" cellspacing="0">
        <tr>
            <td>
                <div class="kpi-box">
                    <div class="kpi-label">Total income</div>
                    <div class="kpi-value">TZS {{ number_format((float) ($summary['totalIncome'] ?? 0), 2) }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-box">
                    <div class="kpi-label">Total expenditure</div>
                    <div class="kpi-value">TZS {{ number_format((float) ($summary['totalExpenditure'] ?? 0), 2) }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-box">
                    <div class="kpi-label">Net balance</div>
                    <div class="kpi-value">TZS {{ number_format((float) ($summary['net'] ?? 0), 2) }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-box">
                    <div class="kpi-label">Rows</div>
                    <div class="kpi-value" style="font-size: 9pt;">In: {{ (int) ($summary['totalIncomeRows'] ?? 0) }} · Exp: {{ (int) ($summary['totalExpenseRows'] ?? 0) }}</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <h2 class="section-title">Income transactions</h2>
    @if (empty($incomeRows))
        <p class="muted">No income records for this period.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Farm / Farmer</th>
                    <th>Status</th>
                    <th class="num">Amount (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($incomeRows as $row)
                    <tr>
                        <td>{{ $row['type'] }}</td>
                        <td>{{ $row['date'] ?: '—' }}</td>
                        <td>{{ $row['reference'] }}</td>
                        <td>{{ $row['farm'] }} @if (!empty($row['farmer'])) · {{ $row['farmer'] }} @endif</td>
                        <td>{{ ucfirst((string) $row['status']) }}</td>
                        <td class="num">{{ number_format((float) $row['amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="section">
    <h2 class="section-title">Expenditure transactions</h2>
    @if (empty($expenseRows))
        <p class="muted">No expenditure records for this period.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Subject</th>
                    <th>Farm / Farmer</th>
                    <th>Status</th>
                    <th class="num">Amount (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenseRows as $row)
                    <tr>
                        <td>{{ $row['type'] }}</td>
                        <td>{{ $row['date'] ?: '—' }}</td>
                        <td>{{ $row['reference'] }}</td>
                        <td>{{ $row['subject'] ?? '—' }}</td>
                        <td>{{ $row['farm'] }} @if (!empty($row['farmer'])) · {{ $row['farmer'] }} @endif</td>
                        <td>{{ ucfirst((string) $row['status']) }}</td>
                        <td class="num">{{ number_format((float) $row['amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="section">
    <h2 class="section-title">Bills preview</h2>
    @if (empty($billRows))
        <p class="muted">No bills for this period.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Bill no.</th>
                    <th>Date</th>
                    <th>Subject</th>
                    <th>Qty</th>
                    <th>Farm / Farmer</th>
                    <th>Status</th>
                    <th class="num">Amount (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($billRows as $row)
                    <tr>
                        <td>{{ $row['billNo'] ?: '—' }}</td>
                        <td>{{ $row['createdAt'] ?: '—' }}</td>
                        <td>{{ $row['subjectType'] ?: '—' }}</td>
                        <td>{{ (int) ($row['quantity'] ?? 1) }}</td>
                        <td>{{ $row['farm'] }} @if (!empty($row['farmer'])) · {{ $row['farmer'] }} @endif</td>
                        <td>{{ ucfirst((string) $row['status']) }}</td>
                        <td class="num">{{ number_format((float) $row['amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="footer">
    {{ $brandName }} — Confidential. Income &amp; Expenditure Report.
</div>
