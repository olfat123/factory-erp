<?php

namespace App\Filament\Pages;

use App\Models\JournalEntry;
use App\Models\Material;
use App\Models\MaterialCategory;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected string $view = 'filament.pages.financial-report';
    protected static ?int $navigationSort = 12;

    public string $dateFrom = '';
    public string $dateTo   = '';

    public function mount(): void
    {
        $this->dateFrom = now()->subMonths(6)->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.reports.financial_report');
    }

    public function getTitle(): string
    {
        return __('resources.reports.financial_report');
    }

    // ---------------------------------------------------------
    // Stat cards
    // ---------------------------------------------------------
    public function getTotalInventoryValue(): float
    {
        return (float) Material::where('is_active', true)
            ->selectRaw('SUM(current_stock * average_cost) as total')
            ->value('total') ?? 0;
    }

    public function getTotalJournalAmount(): float
    {
        return (float) JournalEntry::whereBetween('posted_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->sum('total_amount');
    }

    public function getTotalAccountsPayable(): float
    {
        return (float) JournalEntry::where('type', 'goods_received')
            ->whereBetween('posted_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->sum('total_amount');
    }

    // ---------------------------------------------------------
    // Chart: inventory value by category (doughnut)
    // ---------------------------------------------------------
    public function getValueByCategoryChartData(): string
    {
        $data = MaterialCategory::with(['materials' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->map(fn ($cat) => [
                'name'  => $cat->name,
                'value' => round($cat->materials->sum(fn ($m) => (float) $m->current_stock * (float) $m->average_cost), 2),
            ])
            ->filter(fn ($row) => $row['value'] > 0);

        $colors = ['#3b82f6', '#8b5cf6', '#f59e0b', '#22c55e', '#ef4444', '#06b6d4', '#f97316', '#ec4899'];

        return json_encode([
            'labels'   => $data->pluck('name')->values()->toArray(),
            'datasets' => [[
                'data'            => $data->pluck('value')->values()->toArray(),
                'backgroundColor' => array_slice($colors, 0, $data->count()),
            ]],
        ]);
    }

    // ---------------------------------------------------------
    // Chart: journal entry amounts over time (bar by month)
    // ---------------------------------------------------------
    public function getJournalTrendChartData(): string
    {
        $rows = JournalEntry::select(
                DB::raw("DATE_FORMAT(posted_at, '%Y-%m') as month"),
                'type',
                DB::raw('SUM(total_amount) as total')
            )
            ->whereNotNull('posted_at')
            ->whereBetween('posted_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        $months = $rows->keys()->toArray();
        $types  = ['goods_received', 'production_consume', 'production_output'];
        $colors = ['#22c55e', '#f59e0b', '#8b5cf6'];

        $datasets = [];
        foreach ($types as $idx => $type) {
            $datasets[] = [
                'label'           => __('resources.journal_types.' . $type),
                'data'            => collect($months)->map(fn ($m) => round((float) ($rows[$m]?->firstWhere('type', $type)?->total ?? 0), 2))->toArray(),
                'backgroundColor' => $colors[$idx],
                'borderRadius'    => 4,
            ];
        }

        return json_encode([
            'labels'   => $months,
            'datasets' => $datasets,
        ]);
    }

    // ---------------------------------------------------------
    // Table: inventory valuation
    // ---------------------------------------------------------
    public function getValuationItems(): Collection
    {
        return Material::with('category', 'unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Material $m) => [
                'code'        => $m->code,
                'name'        => app()->getLocale() === 'ar' && $m->name_ar ? $m->name_ar : $m->name,
                'category'    => $m->category?->name,
                'unit'        => $m->unit?->abbreviation,
                'stock'       => (float) $m->current_stock,
                'avg_cost'    => (float) $m->average_cost,
                'total_value' => round((float) $m->current_stock * (float) $m->average_cost, 2),
            ]);
    }

    // ---------------------------------------------------------
    // Table: recent journal entries
    // ---------------------------------------------------------
    public function getRecentJournalEntries(): Collection
    {
        return JournalEntry::with('creator')
            ->whereBetween('posted_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->orderByDesc('posted_at')
            ->limit(50)
            ->get()
            ->map(fn (JournalEntry $e) => [
                'reference'  => $e->reference_number,
                'type'       => __('resources.journal_types.' . $e->type),
                'amount'     => (float) $e->total_amount,
                'posted_at'  => $e->posted_at?->format('Y-m-d'),
                'created_by' => $e->creator?->name ?? '—',
            ]);
    }
}
