<?php

namespace App\Filament\Widgets;

use App\Models\Material;
use App\Services\InventoryService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlertsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('resources.dashboard.low_stock_alerts'))
            ->query(
                Material::query()
                    ->where('is_active', true)
                    ->whereColumn('current_stock', '<=', 'minimum_stock')
                    ->with(['category', 'unit'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(__('resources.dashboard.col_code')),
                Tables\Columns\TextColumn::make('name')->label(__('resources.dashboard.col_name'))
                    ->formatStateUsing(fn ($record) => app()->getLocale() === 'ar' ? ($record->name_ar ?: $record->name) : $record->name),
                Tables\Columns\TextColumn::make('category.name')->label(__('resources.dashboard.col_category'))
                    ->formatStateUsing(fn ($state, $record) => app()->getLocale() === 'ar' ? ($record->category?->name_ar ?: $record->category?->name) : $record->category?->name),
                Tables\Columns\TextColumn::make('current_stock')->label(__('resources.dashboard.col_current_stock'))
                    ->numeric(decimalPlaces: 2)
                    ->color('danger'),
                Tables\Columns\TextColumn::make('minimum_stock')->label(__('resources.dashboard.col_minimum_stock'))->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('unit.symbol')->label(__('resources.dashboard.col_unit')),
            ])
            ->emptyStateHeading(__('resources.dashboard.no_low_stock'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
