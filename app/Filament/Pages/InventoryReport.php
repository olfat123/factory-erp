<?php

namespace App\Filament\Pages;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\StockMovement;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected string $view = 'filament.pages.inventory-report';
    protected static ?int $navigationSort = 10;

    public string $dateFrom = '';
    public string $dateTo   = '';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.reports.inventory_report');
    }

    public function getTitle(): string
    {
        return __('resources.reports.inventory_report');
    }

    // ---------------------------------------------------------
    // Stat cards
    // ---------------------------------------------------------
    public function getTotalMaterials(): int
    {
        return Material::where('is_active', true)->count();
    }

    public function getLowStockCount(): int
    {
        return Material::where('is_active', true)
            ->whereRaw('current_stock <= minimum_stock')
            ->count();
    }

    public function getTotalStockValue(): float
    {
        return (float) Material::where('is_active', true)
            ->selectRaw('SUM(current_stock * average_cost) as total')
            ->value('total') ?? 0;
    }

    // ---------------------------------------------------------
    // Chart: stock by category (bar)
    // ---------------------------------------------------------
    public function getStockByCategoryChartData(): string
    {
        $data = MaterialCategory::withSum('materials as total_stock', 'current_stock')
            ->orderByDesc('total_stock')
            ->limit(10)
            ->get();

        return json_encode([
            'labels'   => $data->pluck('name')->toArray(),
            'datasets' => [[
                'label'           => __('resources.fields.current_stock'),
                'data'            => $data->pluck('total_stock')->map(fn ($v) => round((float) $v, 2))->toArray(),
                'backgroundColor' => '#3b82f6',
                'borderRadius'    => 4,
            ]],
        ]);
    }

    // ---------------------------------------------------------
    // Chart: daily movements last 30 days (line)
    // ---------------------------------------------------------
    public function getDailyMovementsChartData(): string
    {
        $rows = StockMovement::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN movement_type IN ("purchase_receive","production_output","adjustment_increase","return") THEN quantity ELSE 0 END) as inflow'),
                DB::raw('SUM(CASE WHEN movement_type IN ("production_consume","adjustment_decrease") THEN quantity ELSE 0 END) as outflow')
            )
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return json_encode([
            'labels'   => $rows->pluck('date')->toArray(),
            'datasets' => [
                [
                    'label'       => __('resources.reports.inflow'),
                    'data'        => $rows->pluck('inflow')->map(fn ($v) => round((float) $v, 2))->toArray(),
                    'borderColor' => '#22c55e',
                    'tension'     => 0.3,
                    'fill'        => false,
                ],
                [
                    'label'       => __('resources.reports.outflow'),
                    'data'        => $rows->pluck('outflow')->map(fn ($v) => round((float) $v, 2))->toArray(),
                    'borderColor' => '#ef4444',
                    'tension'     => 0.3,
                    'fill'        => false,
                ],
            ],
        ]);
    }

    // ---------------------------------------------------------
    // Table data
    // ---------------------------------------------------------
    public function getMaterials(): Collection
    {
        return Material::with('category', 'unit')
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn (Material $m) => [
                'code'          => $m->code,
                'name'          => $m->translated_name,
                'category'      => $m->category?->translated_name,
                'unit'          => $m->unit?->abbreviation,
                'current_stock' => (float) $m->current_stock,
                'minimum_stock' => (float) $m->minimum_stock,
                'average_cost'  => (float) $m->average_cost,
                'total_value'   => round((float) $m->current_stock * (float) $m->average_cost, 2),
                'market_cost'   => (float) $m->market_cost,
                'market_value'  => round((float) $m->current_stock * (float) $m->market_cost, 2),
                'is_low'        => (float) $m->current_stock <= (float) $m->minimum_stock,
            ]);
    }
}
