<?php

namespace App\Filament\Widgets;

use App\Enums\PurchaseOrderStatus;
use App\Enums\ProductionOrderStatus;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\ProductionOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make(__('resources.dashboard.low_stock_materials'), Material::where('is_active', true)->whereColumn('current_stock', '<=', 'minimum_stock')->count())
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make(__('resources.dashboard.pending_purchase_orders'), PurchaseOrder::where('status', PurchaseOrderStatus::Approved)->count())
                ->color('warning')
                ->icon('heroicon-o-shopping-cart'),

            Stat::make(__('resources.dashboard.active_production_orders'), ProductionOrder::whereIn('status', [ProductionOrderStatus::Approved, ProductionOrderStatus::InProduction])->count())
                ->color('info')
                ->icon('heroicon-o-beaker'),

            Stat::make(__('resources.dashboard.total_materials'), Material::where('is_active', true)->count())
                ->color('success')
                ->icon('heroicon-o-cube'),
        ];
    }
}
