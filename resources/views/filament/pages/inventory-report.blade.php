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
        <button wire:click="$set('dateFrom', '{{ now()->subDays(30)->format('Y-m-d') }}')" onclick="this.closest('div').querySelectorAll('input')[0].value='{{ now()->subDays(30)->format('Y-m-d') }}'" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">30d</button>
        <button wire:click="$set('dateFrom', '{{ now()->subDays(90)->format('Y-m-d') }}')" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">90d</button>
        <button wire:click="$set('dateFrom', '{{ now()->startOfYear()->format('Y-m-d') }}')" style="padding:0.375rem 0.75rem;background:#e5e7eb;border:none;border-radius:0.375rem;font-size:0.8rem;cursor:pointer;">{{ __('resources.reports.this_year') }}</button>
        <span style="font-size:0.8rem;color:#6b7280;margin-left:auto;">{{ $this->dateFrom }} → {{ $this->dateTo }}</span>
    </div>

    {{-- Action bar --}}
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem;" class="no-print">
        <a href="{{ route('reports.inventory.excel', ['from' => $this->dateFrom, 'to' => $this->dateTo]) }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#16a34a;color:#fff;border-radius:0.375rem;font-size:0.875rem;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            {{ __('resources.reports.export_inventory_excel') }}
        </a>
        <a href="{{ route('reports.stock-movements.excel', ['from' => $this->dateFrom, 'to' => $this->dateTo]) }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#2563eb;color:#fff;border-radius:0.375rem;font-size:0.875rem;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            {{ __('resources.reports.export_movements_excel') }}
        </a>
        <a href="{{ route('reports.inventory.pdf', ['from' => $this->dateFrom, 'to' => $this->dateTo]) }}"
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
        @include('filament.partials.stat-card', ['label' => __('resources.reports.total_materials'), 'value' => $this->getTotalMaterials(), 'color' => '#3b82f6'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.low_stock_count'), 'value' => $this->getLowStockCount(), 'color' => '#ef4444'])
        @include('filament.partials.stat-card', ['label' => __('resources.reports.total_stock_value'), 'value' => number_format($this->getTotalStockValue(), 2), 'color' => '#22c55e'])
    </div>

    {{-- Charts --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:1.5rem;margin-bottom:1.5rem;">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.stock_by_category') }}</h3>
            <canvas id="stockByCategoryChart" height="220"></canvas>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.daily_movements_range') }} ({{ $this->dateFrom }} → {{ $this->dateTo }})</h3>
            <canvas id="dailyMovementsChart" height="220"></canvas>
        </div>
    </div>

    {{-- Materials table --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;overflow-x:auto;">
        <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.materials_detail') }}</h3>
        <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.code') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.name') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.category') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.current_stock') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.minimum_stock') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.average_cost') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.total_value') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:center;">{{ __('resources.reports.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getMaterials() as $m)
                    <tr style="border-bottom:1px solid #f3f4f6;{{ $m['is_low'] ? 'background:#fef2f2;' : '' }}">
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $m['code'] }}</td>
                        <td style="padding:0.45rem 0.75rem;font-weight:500;">{{ $m['name'] }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $m['category'] }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($m['current_stock'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;color:#6b7280;">{{ number_format($m['minimum_stock'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($m['average_cost'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;font-weight:600;">{{ number_format($m['total_value'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:center;">
                            @if($m['is_low'])
                                <span style="background:#fee2e2;color:#dc2626;padding:0.15rem 0.5rem;border-radius:999px;font-size:0.75rem;">{{ __('resources.reports.low_stock') }}</span>
                            @else
                                <span style="background:#dcfce7;color:#16a34a;padding:0.15rem 0.5rem;border-radius:999px;font-size:0.75rem;">{{ __('resources.reports.normal') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:1.5rem;text-align:center;color:#9ca3af;">{{ __('resources.pages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Chart.js (loaded once) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @script
    <script>
        (function () {
            ['_rpt_inv_cat', '_rpt_inv_mov'].forEach(k => {
                if (window[k]) { window[k].destroy(); delete window[k]; }
            });

            const catEl = document.getElementById('stockByCategoryChart');
            const movEl = document.getElementById('dailyMovementsChart');

            if (catEl && typeof Chart !== 'undefined') {
                window._rpt_inv_cat = new Chart(catEl, {
                    type: 'bar',
                    data: {!! $this->getStockByCategoryChartData() !!},
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });
            }

            if (movEl && typeof Chart !== 'undefined') {
                window._rpt_inv_mov = new Chart(movEl, {
                    type: 'line',
                    data: {!! $this->getDailyMovementsChartData() !!},
                    options: { responsive: true, plugins: { legend: { position: 'top' } } }
                });
            }
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
        <a href="{{ route('reports.inventory.excel') }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#16a34a;color:#fff;border-radius:0.375rem;font-size:0.875rem;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" style="width:1rem;height:1rem;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            {{ __('resources.reports.export_inventory_excel') }}
        </a>
        <a href="{{ route('reports.stock-movements.excel') }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#2563eb;color:#fff;border-radius:0.375rem;font-size:0.875rem;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            {{ __('resources.reports.export_movements_excel') }}
        </a>
        <a href="{{ route('reports.inventory.pdf') }}"
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
        @include('filament.partials.stat-card', [
            'label' => __('resources.reports.total_materials'),
            'value' => $this->getTotalMaterials(),
            'color' => '#3b82f6',
        ])
        @include('filament.partials.stat-card', [
            'label' => __('resources.reports.low_stock_count'),
            'value' => $this->getLowStockCount(),
            'color' => '#ef4444',
        ])
        @include('filament.partials.stat-card', [
            'label' => __('resources.reports.total_stock_value'),
            'value' => number_format($this->getTotalStockValue(), 2),
            'color' => '#22c55e',
        ])
    </div>

    {{-- Charts --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:1.5rem;margin-bottom:1.5rem;">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.stock_by_category') }}</h3>
            <canvas id="stockByCategoryChart" height="220"></canvas>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;">
            <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.daily_movements_30d') }}</h3>
            <canvas id="dailyMovementsChart" height="220"></canvas>
        </div>
    </div>

    {{-- Materials table --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;overflow-x:auto;">
        <h3 style="font-size:0.9rem;font-weight:600;margin:0 0 1rem;color:#374151;">{{ __('resources.reports.materials_detail') }}</h3>
        <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.code') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.name') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:start;">{{ __('resources.fields.category') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.current_stock') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.minimum_stock') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.average_cost') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.total_value') }}</th>
                    <th style="padding:0.5rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:center;">{{ __('resources.reports.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getMaterials() as $m)
                    <tr style="border-bottom:1px solid #f3f4f6;{{ $m['is_low'] ? 'background:#fef2f2;' : '' }}">
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $m['code'] }}</td>
                        <td style="padding:0.45rem 0.75rem;font-weight:500;">{{ $m['name'] }}</td>
                        <td style="padding:0.45rem 0.75rem;color:#6b7280;">{{ $m['category'] }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($m['current_stock'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;color:#6b7280;">{{ number_format($m['minimum_stock'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;">{{ number_format($m['average_cost'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:end;font-weight:600;">{{ number_format($m['total_value'], 2) }}</td>
                        <td style="padding:0.45rem 0.75rem;text-align:center;">
                            @if($m['is_low'])
                                <span style="background:#fee2e2;color:#dc2626;padding:0.15rem 0.5rem;border-radius:999px;font-size:0.75rem;">{{ __('resources.reports.low_stock') }}</span>
                            @else
                                <span style="background:#dcfce7;color:#16a34a;padding:0.15rem 0.5rem;border-radius:999px;font-size:0.75rem;">{{ __('resources.reports.normal') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:1.5rem;text-align:center;color:#9ca3af;">{{ __('resources.pages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cat = {!! $this->getStockByCategoryChartData() !!};
            new Chart(document.getElementById('stockByCategoryChart'), {
                type: 'bar',
                data: cat,
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            const mov = {!! $this->getDailyMovementsChartData() !!};
            new Chart(document.getElementById('dailyMovementsChart'), {
                type: 'line',
                data: mov,
                options: { responsive: true, plugins: { legend: { position: 'top' } } }
            });
        });
    </script>

    <style>
        @media print {
            .no-print, nav, header, aside, [x-data], .fi-sidebar, .fi-topbar { display: none !important; }
            body { font-size: 11px; }
            canvas { max-width: 100% !important; }
        }
    </style>
</x-filament-panels::page>
