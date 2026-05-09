<?php

namespace App\Filament\Pages;

use App\Models\Material;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class InventoryValuationReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected string $view = 'filament.pages.inventory-valuation-report';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.accounting');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.pages.inventory_valuation');
    }

    public function getTitle(): string
    {
        return __('resources.pages.inventory_valuation');
    }

    public function getItems(): Collection
    {
        return Material::with('category', 'unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Material $m) => [
                'code'          => $m->code,
                'name'          => app()->getLocale() === 'ar' && $m->name_ar ? $m->name_ar : $m->name,
                'category'      => $m->category?->name,
                'unit'          => $m->unit?->abbreviation,
                'current_stock' => (float) $m->current_stock,
                'average_cost'  => (float) $m->average_cost,
                'total_value'   => (float) $m->current_stock * (float) $m->average_cost,
            ]);
    }

    public function getTotalValue(): float
    {
        return $this->getItems()->sum('total_value');
    }
}
