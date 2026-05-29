<x-filament-panels::page>
    <style>
        @@media print {
            .no-print {
                display: none !important;
            }
        }

        /* Force one row: Reset/Print left, period presets right (survives narrow Filament section layouts) */
        .ie-report-actions {
            display: flex;
            width: 100%;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .ie-report-actions__left,
        .ie-report-actions__right {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .ie-report-actions__right {
            margin-inline-start: auto;
        }

        /* Four equal columns, full width of the section (inputs stretch within each cell). */
        .ie-filter-fields {
            display: grid;
            width: 100%;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem 1.5rem;
            align-items: end;
            padding-bottom: 20px;
        }

        @@media (max-width: 1024px) {
            .ie-filter-fields {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @@media (max-width: 640px) {
            .ie-filter-fields {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .ie-filter-fields > .ie-filter-field {
            min-width: 0;
        }

        .ie-filter-fields .fi-input-wrp {
            width: 100%;
            max-width: 100%;
        }

        .ie-filter-fields .fi-input {
            width: 100%;
            min-width: 0;
        }

        .ie-filter-fields .fi-select-input {
            width: 100%;
            min-width: 0;
        }

        /*
         * KPI stats: Filament cards render as section.fi-section. Using those as direct grid
         * children can make borders look stacked with no visible gap. Wrap each card in
         * .ie-kpi-cell and use explicit row/column gap (20px) on .ie-kpi-grid.
         */
        .ie-kpi-grid {
            display: grid;
            width: 100%;
            grid-template-columns: 1fr;
            row-gap: 20px;
            column-gap: 20px;
        }

        @@media (min-width: 768px) {
            .ie-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @@media (min-width: 1280px) {
            .ie-kpi-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .ie-kpi-cell {
            min-width: 0;
        }

        .ie-kpi-cell > .fi-section {
            height: 100%;
        }

        .ie-report-stack {
            width: 100%;
        }

        .ie-report-section + .ie-report-section {
            margin-top: 2.5rem !important;
        }

        .ie-transaction-card {
            margin-bottom: 20px !important;
            border: 1px solid rgb(229 231 235 / 0.9);
            background: rgb(255 255 255 / 0.95);
            box-shadow: 0 1px 2px rgb(15 23 42 / 0.05);
        }

        .dark .ie-transaction-card {
            border-color: rgb(55 65 81 / 0.9);
            background: rgb(17 24 39 / 0.45);
        }

        .ie-transaction-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .ie-transaction-label {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: rgb(107 114 128);
        }

        .ie-transaction-value {
            font-size: 0.9375rem;
            font-weight: 600;
            color: rgb(17 24 39);
            line-height: 1.4;
        }

        .dark .ie-transaction-value {
            color: rgb(243 244 246);
        }

        .ie-transaction-amount {
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.15;
        }

        .ie-transaction-grid {
            display: grid;
            gap: 1rem;
        }

        @@media (min-width: 1024px) {
            .ie-transaction-grid {
                grid-template-columns: minmax(0, 1fr) 14rem;
                align-items: start;
            }
        }

        .ie-transaction-fields {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 1.5rem;
        }

        .ie-transaction-field {
            min-width: 0;
            flex: 1 1 14rem;
        }

        .ie-transaction-field--wide {
            flex-basis: 100%;
        }

        @@media (min-width: 1024px) {
            .ie-transaction-field--wide {
                flex-basis: 100%;
            }
        }

        /* Same PDF document markup is hidden on screen; shown only when printing */
        .ie-print-only {
            display: none !important;
        }

        @@media print {
            @@page {
                size: A4;
                margin: 12mm;
            }

            /*
             * Do NOT set print-color-adjust: exact on body — in dark mode Filament paints
             * html/body/sidebar/topbar with dark backgrounds; exact would print them as black bars.
             * Only the report subtree needs exact colors (KPI greys, table headers).
             */
            html,
            html.fi,
            html.dark,
            body,
            body.fi-body {
                background: #fff !important;
                background-color: #fff !important;
                color: #111827 !important;
                color-scheme: light !important;
                print-color-adjust: economy;
                -webkit-print-color-adjust: economy;
            }

            .fi-sidebar,
            .fi-sidebar-close-overlay,
            .fi-topbar,
            .fi-topbar-close-sidebar-btn,
            .fi-layout-sidebar-toggle-btn-ctn {
                display: none !important;
            }

            .fi-layout,
            .fi-main-ctn,
            .fi-main,
            .fi-page,
            .fi-page-header-main-ctn,
            .fi-page-main,
            .fi-page-content {
                background: #fff !important;
                background-color: #fff !important;
                background-image: none !important;
                box-shadow: none !important;
            }

            .fi-main-ctn,
            .fi-main {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print,
            .no-print * {
                display: none !important;
            }

            body * {
                visibility: hidden !important;
            }

            #ie-report-pdf-print,
            #ie-report-pdf-print * {
                visibility: visible !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            #ie-report-pdf-print {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                background-color: #fff !important;
            }

            .ie-print-only {
                display: block !important;
                visibility: visible !important;
            }
        }
    </style>

    <x-filament::section class="no-print" heading="Filters" description="Refine report by date range, farm, and farmer (Livewire-bound)." icon="heroicon-o-funnel">
        <div class="space-y-6" wire:key="ie-filters">
            <div class="ie-filter-fields">
                <div class="space-y-2 ie-filter-field fi-fo-field">
                    <label class="fi-fo-field-label" for="ie-from-date">
                        <span class="fi-fo-field-label-content">From date</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            id="ie-from-date"
                            type="date"
                            wire:model.live="fromDate"
                        />
                    </x-filament::input.wrapper>
                </div>
                <div class="space-y-2 ie-filter-field fi-fo-field">
                    <label class="fi-fo-field-label" for="ie-to-date">
                        <span class="fi-fo-field-label-content">To date</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            id="ie-to-date"
                            type="date"
                            wire:model.live="toDate"
                        />
                    </x-filament::input.wrapper>
                </div>
                <div class="space-y-2 ie-filter-field fi-fo-field">
                    <label class="fi-fo-field-label" for="ie-farm">
                        <span class="fi-fo-field-label-content">Farm</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select id="ie-farm" wire:model.live="farmUuid">
                            <option value="">All farms</option>
                            @foreach ($this->farmOptions() as $uuid => $name)
                                <option value="{{ $uuid }}">{{ $name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                <div class="space-y-2 ie-filter-field fi-fo-field">
                    <label class="fi-fo-field-label" for="ie-farmer">
                        <span class="fi-fo-field-label-content">Farmer</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select id="ie-farmer" wire:model.live="farmerId">
                            <option value="">All farmers</option>
                            @foreach ($this->farmerOptions() as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
            <div class="p-3 py-4 overflow-x-auto border border-gray-200 rounded-xl dark:border-gray-700">
                <div class="ie-report-actions min-w-[min(100%,36rem)]">
                    <div class="ie-report-actions__left">
                        <x-filament::button size="sm" color="gray" wire:click="resetFilters" icon="heroicon-o-arrow-path">
                            Reset
                        </x-filament::button>
                    </div>
                    <div class="ie-report-actions__right">
                        <x-filament::button size="sm" color="gray" wire:click="setDatePreset('month')">
                            This Month
                        </x-filament::button>
                        <x-filament::button size="sm" color="gray" wire:click="setDatePreset('week')">
                            This Week
                        </x-filament::button>
                        <x-filament::button size="sm" color="gray" wire:click="setDatePreset('today')">
                            Today
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>

    <div id="ie-report-document" class="ie-report-document no-print">
        <div class="ie-report-stack">
            <div class="ie-report-section">
            <x-filament::section
                heading="Income & Expenditure Report"
                description="Summary totals and detailed transactions for the selected filters."
                icon="heroicon-o-document-chart-bar"
                wire:key="ie-summary-{{ $fromDate }}-{{ $toDate }}-{{ $farmUuid }}-{{ $farmerId }}"
            >
                <div wire:loading.delay class="flex items-center gap-2 mb-4 text-sm no-print fi-text-color-500">
                    <x-filament::loading-indicator class="w-5 h-5" />
                    <span>Updating report…</span>
                </div>
                <div class="ie-kpi-grid">
                    <div class="ie-kpi-cell">
                        <x-filament::card>
                            <p class="fi-text-sm fi-font-medium fi-text-color-500">Total Income</p>
                            <p class="fi-text-2xl fi-font-bold">TZS {{ number_format((float) ($summary['totalIncome'] ?? 0), 2) }}</p>
                        </x-filament::card>
                    </div>
                    <div class="ie-kpi-cell">
                        <x-filament::card>
                            <p class="fi-text-sm fi-font-medium fi-text-color-500">Total Expenditure</p>
                            <p class="fi-text-2xl fi-font-bold">TZS {{ number_format((float) ($summary['totalExpenditure'] ?? 0), 2) }}</p>
                        </x-filament::card>
                    </div>
                    <div class="ie-kpi-cell">
                        <x-filament::card>
                            <p class="fi-text-sm fi-font-medium fi-text-color-500">Net Balance</p>
                            <p class="fi-text-2xl fi-font-bold">TZS {{ number_format((float) ($summary['net'] ?? 0), 2) }}</p>
                        </x-filament::card>
                    </div>
                    <div class="ie-kpi-cell">
                        <x-filament::card>
                            <p class="fi-text-sm fi-font-medium fi-text-color-500">Rows</p>
                            <p class="fi-text-sm fi-font-semibold">Income: {{ (int) ($summary['totalIncomeRows'] ?? 0) }}</p>
                            <p class="fi-text-sm fi-font-semibold">Expenses: {{ (int) ($summary['totalExpenseRows'] ?? 0) }}</p>
                        </x-filament::card>
                    </div>
                </div>
            </x-filament::section>
            </div>

            <div class="ie-report-section">
            <x-filament::section heading="Income Transactions" icon="heroicon-o-arrow-trending-up">
                @if (empty($incomeRows))
                    <x-filament::empty-state
                        heading="No income records found"
                        description="Try adjusting date range or removing farm/farmer filters."
                        icon="heroicon-o-banknotes"
                    />
                @else
                    <div class="space-y-4">
                        @foreach ($incomeRows as $row)
                            <x-filament::card class="ie-transaction-card">
                                <div class="ie-transaction-grid">
                                    <div class="min-w-0 space-y-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <x-filament::badge size="sm" color="success">
                                                {{ $row['type'] }}
                                            </x-filament::badge>
                                            <x-filament::badge size="sm" color="gray">
                                                {{ $row['date'] ?: 'No date' }}
                                            </x-filament::badge>
                                            <x-filament::badge size="sm" color="{{ $row['status'] === 'received' || $row['status'] === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst((string) $row['status']) }}
                                            </x-filament::badge>
                                        </div>

                                        <div class="ie-transaction-fields" style="padding-top: 10px;">
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Farm</div>
                                                <div class="ie-transaction-value">{{ $row['farm'] ?: '—' }}</div>
                                            </div>
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Farmer</div>
                                                <div class="ie-transaction-value">{{ $row['farmer'] ?: '—' }}</div>
                                            </div>
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Record</div>
                                                <div class="ie-transaction-value">{{ $row['reference'] ?: 'Income record' }}</div>
                                            </div>
                                            @if (!empty($row['referenceCode']) && $row['referenceCode'] !== $row['reference'])
                                                <div class="space-y-1 ie-transaction-field">
                                                    <div class="ie-transaction-label">Code</div>
                                                    <div class="ie-transaction-value">{{ $row['referenceCode'] }}</div>
                                                </div>
                                            @endif
                                            @if (!empty($row['subject']) && $row['subject'] !== '-')
                                                <div class="space-y-1 ie-transaction-field ie-transaction-field--wide">
                                                    <div class="ie-transaction-label">Subject</div>
                                                    <div class="ie-transaction-value">{{ $row['subject'] }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex min-w-[11rem] flex-col items-start gap-2 rounded-2xl bg-gray-50 px-4 py-3 lg:items-end lg:text-right dark:bg-gray-900/60">
                                        <div class="ie-transaction-label">Amount</div>
                                        <p class="ie-transaction-amount">TZS {{ number_format((float) $row['amount'], 2) }}</p>
                                    </div>
                                </div>
                            </x-filament::card>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
            </div>

            <div class="ie-report-section">
            <x-filament::section heading="Expenditure Transactions" icon="heroicon-o-arrow-trending-down">
                @if (empty($expenseRows))
                    <x-filament::empty-state
                        heading="No expenditure records found"
                        description="No expense entries match current filters."
                        icon="heroicon-o-receipt-refund"
                    />
                @else
                    <div class="space-y-4">
                        @foreach ($expenseRows as $row)
                            <x-filament::card class="ie-transaction-card">
                                <div class="ie-transaction-grid">
                                    <div class="min-w-0 space-y-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <x-filament::badge size="sm" color="danger">
                                                {{ $row['type'] }}
                                            </x-filament::badge>
                                            <x-filament::badge size="sm" color="gray">
                                                {{ $row['date'] ?: 'No date' }}
                                            </x-filament::badge>
                                            <x-filament::badge size="sm" color="{{ $row['status'] === 'received' || $row['status'] === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst((string) $row['status']) }}
                                            </x-filament::badge>
                                        </div>

                                        <div class="ie-transaction-fields" style="padding-top: 10px;">
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Farm</div>
                                                <div class="ie-transaction-value">{{ $row['farm'] ?: '—' }}</div>
                                            </div>
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Farmer</div>
                                                <div class="ie-transaction-value">{{ $row['farmer'] ?: '—' }}</div>
                                            </div>
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Record</div>
                                                <div class="ie-transaction-value">{{ $row['reference'] ?: 'Expense record' }}</div>
                                            </div>
                                            @if (!empty($row['referenceCode']) && $row['referenceCode'] !== $row['reference'])
                                                <div class="space-y-1 ie-transaction-field">
                                                    <div class="ie-transaction-label">Code</div>
                                                    <div class="ie-transaction-value">{{ $row['referenceCode'] }}</div>
                                                </div>
                                            @endif
                                            @if (!empty($row['subject']) && $row['subject'] !== '-')
                                                <div class="space-y-1 ie-transaction-field ie-transaction-field--wide">
                                                    <div class="ie-transaction-label">Subject</div>
                                                    <div class="ie-transaction-value">{{ $row['subject'] }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex min-w-[11rem] flex-col items-start gap-2 rounded-2xl bg-gray-50 px-4 py-3 lg:items-end lg:text-right dark:bg-gray-900/60">
                                        <div class="ie-transaction-label">Amount</div>
                                        <p class="ie-transaction-amount">TZS {{ number_format((float) $row['amount'], 2) }}</p>
                                    </div>
                                </div>
                            </x-filament::card>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
            </div>

            <div class="ie-report-section">
            <x-filament::section heading="Bills Preview" icon="heroicon-o-receipt-percent">
                @if (empty($billRows))
                    <x-filament::empty-state
                        heading="No bills to preview"
                        description="No bill records are available for this filter selection."
                        icon="heroicon-o-document-text"
                    />
                @else
                    <div class="space-y-4">
                        @foreach ($billRows as $row)
                            <x-filament::card class="ie-transaction-card">
                                <div class="ie-transaction-grid">
                                    <div class="min-w-0 space-y-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <x-filament::badge size="sm" color="warning">
                                                Bill preview
                                            </x-filament::badge>
                                            <x-filament::badge size="sm" color="gray">
                                                {{ $row['createdAt'] ?: 'No date' }}
                                            </x-filament::badge>
                                            <x-filament::badge size="sm" color="{{ $row['status'] === 'received' || $row['status'] === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst((string) $row['status']) }}
                                            </x-filament::badge>
                                        </div>

                                        <div class="ie-transaction-fields" style="padding-top: 10px;">
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Bill No.</div>
                                                <div class="ie-transaction-value">{{ $row['billNo'] ?: 'Bill record' }}</div>
                                            </div>
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Farm</div>
                                                <div class="ie-transaction-value">{{ $row['farm'] ?: '—' }}</div>
                                            </div>
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Farmer</div>
                                                <div class="ie-transaction-value">{{ $row['farmer'] ?: '—' }}</div>
                                            </div>
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Subject</div>
                                                <div class="ie-transaction-value">{{ $row['subjectType'] ?: '—' }}</div>
                                            </div>
                                            <div class="space-y-1 ie-transaction-field">
                                                <div class="ie-transaction-label">Quantity</div>
                                                <div class="ie-transaction-value">{{ (int) $row['quantity'] }}</div>
                                            </div>
                                            @if (!empty($row['referenceCode']) && $row['referenceCode'] !== $row['billNo'])
                                                <div class="space-y-1 ie-transaction-field">
                                                    <div class="ie-transaction-label">Code</div>
                                                    <div class="ie-transaction-value">{{ $row['referenceCode'] }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex min-w-[11rem] flex-col items-start gap-2 rounded-2xl bg-gray-50 px-4 py-3 lg:items-end lg:text-right dark:bg-gray-900/60">
                                        <div class="ie-transaction-label">Amount</div>
                                        <p class="ie-transaction-amount">TZS {{ number_format((float) $row['amount'], 2) }}</p>
                                    </div>
                                </div>
                            </x-filament::card>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
            </div>
        </div>
    </div>

    @php
        $brandName = \Filament\Facades\Filament::getCurrentPanel()?->getBrandName() ?? config('app.name', 'Report');
        $generatedAt = now();
    @endphp
    <div id="ie-report-pdf-print" class="ie-print-only ie-pdf-print-root" aria-hidden="true">
        @include('pdf.partials.income-expenditure-report-styles')
        @include('pdf.partials.income-expenditure-report-body')
    </div>
</x-filament-panels::page>
