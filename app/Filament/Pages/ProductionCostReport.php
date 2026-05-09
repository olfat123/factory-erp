<?php

namespace App\Filament\Pages;

use App\Models\ProductionOrder;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class ProductionCostReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';
    protected string $view = 'filament.pages.production-cost-report';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.accounting');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.pages.production_cost');
    }

    public function getTitle(): string
    {
        return __('resources.pages.production_cost');
    }

    public function getOrders(): Collection
    {
        return ProductionOrder::with(['product', 'items.material'])
            ->whereIn('status', ['completed', 'in_production'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (ProductionOrder $order) {
                $materialCost = $order->items->sum(
                    fn ($i) => $i->consumed_quantity * ($i->material->average_cost ?? 0)
                );
                $qty = (float) ($order->completed_quantity ?? $order->quantity);

                return [
                    'id'            => $order->id,
                    'product'       => $order->product?->name,
                    'status'        => $order->status,
                    'quantity'      => $qty,
                    'material_cost' => $materialCost,
                    'unit_cost'     => $qty > 0 ? $materialCost / $qty : 0,
                    'created_at'    => $order->created_at,
                ];
            });
    }

    public function getTotalCost(): float
    {
        return $this->getOrders()->sum('material_cost');
    }
}
