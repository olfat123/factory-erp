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
        <button wire:click="$set('dateFrom', '{{ now()->subDays(90)->format('Y-m-d') }}')" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">90d</button>
        <button wire:click="$set('dateFrom', '{{ now()->startOfYear()->format('Y-m-d') }}')" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">{{ __('resources.reports.this_year') }}</button>
        <span style="font-size:0.8rem;color:#6b7280;margin-left:auto;">{{ $this->dateFrom }} → {{ $this->dateTo }}</span>
    </div>

    {{-- Action bar --}}
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem;" class="no-print">
        <a href="{{ route('reports.production.excel', ['from' => $this->dateFrom, 'to' => $this->dateTo]) }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#16a34a;color:#fff;border-radius:0.375rem;font-size:0.875rem;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            {{ __('resources.reports.export_excel') }}
        </a>
        <a href="{{ route('reports.production.pdf', ['from' => $this->dateFrom, 'to' => $this->dateTo]) }}"
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
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
        @include('filament.partials.stat-card', ['label' => __('resources.reports.total_orders'), 'value' => $this->getTotalOrders(), 'color' => '#8b5cf6'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.completed_orders'), 'value' => $this->getCompletedOrders(), 'color' => '#22c55e'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.in_progress_orders'), 'value' => $this->getInProgressOrders(), 'color' => '#f59e0b'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.total_material_cost'), 'value' => number_format($this->getTotalMaterialCost(), 2), 'color' => '#ef4444'])
    </div>

    {{-- Charts row 1 --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:1.5rem;margin-bottom:1.5rem;">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.production_by_product') }}</h3>
            <canvas id="productionByProductChart" height="220"></canvas>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.daily_completions_range') }} ({{ $this->dateFrom }} → {{ $this->dateTo }})</h3>
            <canvas id="dailyCompletionsChart" height="220"></canvas>
        </div>
    </div>

    {{-- Charts row 2 --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;margin-bottom:1.5rem;">
        <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.material_consumption') }}</h3>
        <canvas id="materialConsumptionChart" height="140"></canvas>
    </div>

    {{-- Orders table --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;overflow-x:auto;">
        <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.orders_detail') }}</h3>
        <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">#</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.product') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.status') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.planned_quantity') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.actual_quantity') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.reports.material_cost') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.planned_date') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.reports.completed_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getOrders() as $o)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $o['id'] }}</td>
                        <td style="padding:0.45rem 0.75rem;font-weight:500;">{{ $o['product'] }}</td>
                        <td style="padding:0.45rem 0.75rem;"><span style="padding:0.15rem 0.5rem;border-radius:999px;font-size:0.75rem;background:#f3f4f6;">{{ $o['status'] }}</span></td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($o['quantity'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($o['completed_qty'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;font-weight:600;">{{ number_format($o['material_cost'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $o['planned_date'] ?? '—' }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $o['completed_at'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:1.5rem;text-align:center;color:#9ca3af;">{{ __('resources.pages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @script
    <script>
        (function () {
            ['_rpt_prod_pp', '_rpt_prod_dc', '_rpt_prod_mc'].forEach(k => {
                if (window[k]) { window[k].destroy(); delete window[k]; }
            });

            if (typeof Chart === 'undefined') return;

            const ppEl = document.getElementById('productionByProductChart');
            const dcEl = document.getElementById('dailyCompletionsChart');
            const mcEl = document.getElementById('materialConsumptionChart');

            if (ppEl) window._rpt_prod_pp = new Chart(ppEl, {
                type: 'bar',
                data: {!! $this->getProductionByProductChartData() !!},
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            if (dcEl) window._rpt_prod_dc = new Chart(dcEl, {
                type: 'line',
                data: {!! $this->getDailyCompletionsChartData() !!},
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y:  { type: 'linear', position: 'left' },
                        y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false } }
                    }
                }
            });

            if (mcEl) window._rpt_prod_mc = new Chart(mcEl, {
                type: 'bar',
                data: {!! $this->getMaterialConsumptionChartData() !!},
                options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
            });
        })();
    </script>
    @endscript

    <style>
        @media print {
            .no-print, nav, header, aside, .fi-sidebar, .fi-topbar { display: none !important; }
            body { font-size: 11px; }
        }
    </style>
    {{-- Action bar --}}
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem;" class="no-print">
        <a href="{{ route('reports.production.excel') }}"
           
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#16a34a;color:#fff;border-radius:0.375rem;font-size:0.875rem;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            {{ __('resources.reports.export_excel') }}
        </a>
        <a href="{{ route('reports.production.pdf') }}"
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
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
        @include('filament.partials.stat-card', ['label' => __('resources.reports.total_orders'), 'value' => $this->getTotalOrders(), 'color' => '#8b5cf6'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.completed_orders'), 'value' => $this->getCompletedOrders(), 'color' => '#22c55e'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.in_progress_orders'), 'value' => $this->getInProgressOrders(), 'color' => '#f59e0b'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.total_material_cost'), 'value' => number_format($this->getTotalMaterialCost(), 2), 'color' => '#ef4444'])
    </div>

    {{-- Charts row 1 --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:1.5rem;margin-bottom:1.5rem;">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.production_by_product') }}</h3>
            <canvas id="productionByProductChart" height="220"></canvas>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.daily_completions_60d') }}</h3>
            <canvas id="dailyCompletionsChart" height="220"></canvas>
        </div>
    </div>

    {{-- Charts row 2 --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;margin-bottom:1.5rem;">
        <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.material_consumption') }}</h3>
        <canvas id="materialConsumptionChart" height="140"></canvas>
    </div>

    {{-- Orders table --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;overflow-x:auto;">
        <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.orders_detail') }}</h3>
        <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">#</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.product') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.status') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.planned_quantity') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.actual_quantity') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.reports.material_cost') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.planned_date') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.reports.completed_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getOrders() as $o)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $o['id'] }}</td>
                        <td style="padding:0.45rem 0.75rem;font-weight:500;">{{ $o['product'] }}</td>
                        <td style="padding:0.45rem 0.75rem;"><span style="padding:0.15rem 0.5rem;border-radius:999px;font-size:0.75rem;background:#f3f4f6;">{{ $o['status'] }}</span></td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($o['quantity'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($o['completed_qty'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;font-weight:600;">{{ number_format($o['material_cost'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $o['planned_date'] ?? '—' }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $o['completed_at'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:1.5rem;text-align:center;color:#9ca3af;">{{ __('resources.pages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Chart(document.getElementById('productionByProductChart'), {
                type: 'bar',
                data: {!! $this->getProductionByProductChartData() !!},
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            const dc = {!! $this->getDailyCompletionsChartData() !!};
            new Chart(document.getElementById('dailyCompletionsChart'), {
                type: 'line',
                data: dc,
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y:  { type: 'linear', position: 'left' },
                        y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false } }
                    }
                }
            });

            new Chart(document.getElementById('materialConsumptionChart'), {
                type: 'bar',
                data: {!! $this->getMaterialConsumptionChartData() !!},
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>

    <style>
        @media print {
            .no-print, nav, header, aside, .fi-sidebar, .fi-topbar { display: none !important; }
            body { font-size: 11px; }
            canvas { max-width: 100% !important; }
        }
    </style>
</x-filament-panels::page>
