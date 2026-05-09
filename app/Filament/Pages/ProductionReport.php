<?php

namespace App\Filament\Pages;

use App\Models\ProductionOrder;
use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductionReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected string $view = 'filament.pages.production-report';
    protected static ?int $navigationSort = 11;

    public string $dateFrom = '';
    public string $dateTo   = '';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(90)->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.reports.production_report');
    }

    public function getTitle(): string
    {
        return __('resources.reports.production_report');
    }

    // ---------------------------------------------------------
    // Stat cards
    // ---------------------------------------------------------
    public function getTotalOrders(): int
    {
        return ProductionOrder::whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])->count();
    }

    public function getCompletedOrders(): int
    {
        return ProductionOrder::where('status', 'completed')
            ->whereBetween('completed_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->count();
    }

    public function getInProgressOrders(): int
    {
        return ProductionOrder::where('status', 'in_production')
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->count();
    }

    public function getTotalMaterialCost(): float
    {
        return (float) ProductionOrder::with('items.material')
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->get()
            ->sum(fn ($o) => $o->items->sum(fn ($i) => $i->consumed_quantity * ($i->material?->average_cost ?? 0)));
    }

    // ---------------------------------------------------------
    // Chart: production qty by product (bar)
    // ---------------------------------------------------------
    public function getProductionByProductChartData(): string
    {
        $data = Product::withSum(
                ['productionOrders as total_produced' => fn ($q) => $q
                    ->where('status', 'completed')
                    ->whereBetween('completed_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
                ],
                'completed_quantity'
            )
            ->orderByDesc('total_produced')
            ->limit(12)
            ->get();

        return json_encode([
            'labels'   => $data->pluck('name')->toArray(),
            'datasets' => [[
                'label'           => __('resources.reports.qty_produced'),
                'data'            => $data->pluck('total_produced')->map(fn ($v) => round((float) $v, 2))->toArray(),
                'backgroundColor' => '#8b5cf6',
                'borderRadius'    => 4,
            ]],
        ]);
    }

    // ---------------------------------------------------------
    // Chart: daily completions last 60 days (line)
    // ---------------------------------------------------------
    public function getDailyCompletionsChartData(): string
    {
        $rows = ProductionOrder::select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('SUM(completed_quantity) as qty'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return json_encode([
            'labels'   => $rows->pluck('date')->toArray(),
            'datasets' => [
                [
                    'label'       => __('resources.reports.qty_produced'),
                    'data'        => $rows->pluck('qty')->map(fn ($v) => round((float) $v, 2))->toArray(),
                    'borderColor' => '#8b5cf6',
                    'tension'     => 0.3,
                    'fill'        => false,
                    'yAxisID'     => 'y',
                ],
                [
                    'label'       => __('resources.reports.orders_count'),
                    'data'        => $rows->pluck('orders')->toArray(),
                    'borderColor' => '#f59e0b',
                    'tension'     => 0.3,
                    'fill'        => false,
                    'yAxisID'     => 'y1',
                ],
            ],
        ]);
    }

    // ---------------------------------------------------------
    // Chart: material consumption by material (horizontal bar)
    // ---------------------------------------------------------
    public function getMaterialConsumptionChartData(): string
    {
        $rows = DB::table('production_order_items')
            ->join('materials', 'materials.id', '=', 'production_order_items.material_id')
            ->join('production_orders', 'production_orders.id', '=', 'production_order_items.production_order_id')
            ->select(
                'materials.name',
                DB::raw('SUM(production_order_items.consumed_quantity) as total_consumed')
            )
            ->whereBetween('production_orders.created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->groupBy('materials.id', 'materials.name')
            ->orderByDesc('total_consumed')
            ->limit(10)
            ->get();

        return json_encode([
            'labels'   => $rows->pluck('name')->toArray(),
            'datasets' => [[
                'label'           => __('resources.reports.total_consumed'),
                'data'            => $rows->pluck('total_consumed')->map(fn ($v) => round((float) $v, 2))->toArray(),
                'backgroundColor' => '#f97316',
                'borderRadius'    => 4,
            ]],
        ]);
    }

    // ---------------------------------------------------------
    // Table data
    // ---------------------------------------------------------
    public function getOrders(): Collection
    {
        return ProductionOrder::with(['product', 'items.material'])
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (ProductionOrder $o) => [
                'id'            => $o->id,
                'product'       => $o->product?->name ?? '—',
                'status'        => $o->status instanceof \BackedEnum ? $o->status->value : $o->status,
                'quantity'      => (float) $o->quantity,
                'completed_qty' => (float) ($o->completed_quantity ?? 0),
                'material_cost' => round($o->items->sum(fn ($i) => $i->consumed_quantity * ($i->material?->average_cost ?? 0)), 2),
                'planned_date'  => $o->planned_date?->format('Y-m-d'),
                'completed_at'  => $o->completed_at?->format('Y-m-d'),
            ]);
    }
}
