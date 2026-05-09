<x-filament-panels::page>
    {{-- Date range filter --}}
    <div style="display:flex;align-items:flex-end;gap:0.75rem;flex-wrap:wrap;background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.75rem 1rem;margin-bottom:1rem;" class="no-print">
        <div style="display:flex;flex-direction:column;gap:0.25rem;">
            <label style="font-size:0.75rem;font-weight:500;color:#374151;">{{ __('resources.reports.date_from') }}</label>
            <input type="date" wire:model.live="dateFrom" value="{{ $this->dateFrom }}"
                   style="border:1px solid #d1d5db;border-radius:0.375rem;padding:0.375rem 0.625rem;font-size:0.875rem;color:#111827;">
        </div>
        <div style="display:flex;flex-direction:column;gap:0.25rem;">
            <label style="font-size:0.75rem;font-weight:500;color:#374151;">{{ __('resources.reports.date_to') }}</label>
            <input type="date" wire:model.live="dateTo" value="{{ $this->dateTo }}"
                   style="border:1px solid #d1d5db;border-radius:0.375rem;padding:0.375rem 0.625rem;font-size:0.875rem;color:#111827;">
        </div>
        <button wire:click="$set('dateFrom', '{{ now()->subDays(30)->format('Y-m-d') }}')" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">30d</button>
        <button wire:click="$set('dateFrom', '{{ now()->subMonths(3)->format('Y-m-d') }}')" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">3m</button>
        <button wire:click="$set('dateFrom', '{{ now()->subMonths(6)->format('Y-m-d') }}')" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">6m</button>
        <button wire:click="$set('dateFrom', '{{ now()->startOfYear()->format('Y-m-d') }}')" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">{{ __('resources.reports.this_year') }}</button>
        <span style="font-size:0.8rem;color:#6b7280;margin-left:auto;">{{ $this->dateFrom }} → {{ $this->dateTo }}</span>
    </div>

    {{-- Action bar --}}
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem;" class="no-print">
        <a href="{{ route('reports.financial.excel', ['from' => $this->dateFrom, 'to' => $this->dateTo]) }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#16a34a;color:#fff;border-radius:0.375rem;font-size:0.875rem;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            {{ __('resources.reports.export_excel') }}
        </a>
        <a href="{{ route('reports.financial.pdf', ['from' => $this->dateFrom, 'to' => $this->dateTo]) }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#dc2626;color:#fff;border-radius:0.375rem;font-size:0.875rem;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            {{ __('resources.reports.export_pdf') }}
        </a>
        <button onclick="window.print()"
                style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#6b7280;color:#fff;border-radius:0.375rem;font-size:0.875rem;border:none;cursor:pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            {{ __('resources.reports.print') }}
        </button>
    </div>

    {{-- Stat cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;">
        @include('filament.partials.stat-card', ['label' => __('resources.pages.total_inventory_value'), 'value' => number_format($this->getTotalInventoryValue(), 2), 'color' => '#3b82f6'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.total_journal_amount'), 'value' => number_format($this->getTotalJournalAmount(), 2), 'color' => '#8b5cf6'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.accounts_payable'), 'value' => number_format($this->getTotalAccountsPayable(), 2), 'color' => '#ef4444'])
    </div>

    {{-- Charts --}}
    <div style="display:grid;grid-template-columns:300px 1fr;gap:1.5rem;margin-bottom:1.5rem;align-items:start;">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.value_by_category') }}</h3>
            <canvas id="valueByCategoryChart"></canvas>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">
                {{ __('resources.reports.journal_trend') }} ({{ $this->dateFrom }} → {{ $this->dateTo }})
            </h3>
            <canvas id="journalTrendChart" height="180"></canvas>
        </div>
    </div>

    {{-- Inventory valuation table --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;overflow-x:auto;margin-bottom:1.5rem;">
        <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.pages.inventory_valuation') }}</h3>
        <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.code') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.name') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.category') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.current_stock') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.average_cost') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.total_value') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getValuationItems() as $item)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $item['code'] }}</td>
                        <td style="padding:0.45rem 0.75rem;font-weight:500;">{{ $item['name'] }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $item['category'] }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($item['stock'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($item['avg_cost'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;font-weight:600;">{{ number_format($item['total_value'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:1.5rem;text-align:center;color:#9ca3af;">{{ __('resources.pages.no_data') }}</td></tr>
                @endforelse
                @if($this->getValuationItems()->isNotEmpty())
                    <tr style="background:#f3f4f6;font-weight:700;">
                        <td colspan="5" style="padding:0.5rem 0.75rem;text-align:end;">{{ __('resources.pages.total_inventory_value') }}</td>
                        <td style="padding:0.5rem 0.75rem;text-align:end;">{{ number_format($this->getTotalInventoryValue(), 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Journal entries table --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;overflow-x:auto;">
        <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.recent_journal_entries') }}</h3>
        <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.reference_number') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.entry_type') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.total_amount') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.posted_at') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.created_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getRecentJournalEntries() as $e)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:0.45rem 0.75rem;font-family:monospace;font-size:0.8rem;">{{ $e['reference'] }}</td>
                        <td style="padding:0.45rem 0.75rem;">{{ $e['type'] }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;font-weight:600;">{{ number_format($e['amount'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $e['posted_at'] }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $e['created_by'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:1.5rem;text-align:center;color:#9ca3af;">{{ __('resources.pages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @script
    <script>
        (function () {
            ['_rpt_fin_cat', '_rpt_fin_jt'].forEach(k => {
                if (window[k]) { window[k].destroy(); delete window[k]; }
            });

            if (typeof Chart === 'undefined') return;

            const catEl = document.getElementById('valueByCategoryChart');
            const jtEl  = document.getElementById('journalTrendChart');

            if (catEl) window._rpt_fin_cat = new Chart(catEl, {
                type: 'doughnut',
                data: {!! $this->getValueByCategoryChartData() !!},
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });

            if (jtEl) window._rpt_fin_jt = new Chart(jtEl, {
                type: 'bar',
                data: {!! $this->getJournalTrendChartData() !!},
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: { x: { stacked: false }, y: { stacked: false } }
                }
            });
        })();
    </script>
    @endscript

    <style>
        @media print {
            .no-print, nav, header, aside, .fi-sidebar, .fi-topbar { display: none !important; }
            body { font-size: 11px; }
            canvas { max-width: 100% !important; }
        }
    </style>
</x-filament-panels::page>
