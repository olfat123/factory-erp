<?php

namespace App\Filament\Resources;

use App\Enums\StockMovementType;
use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrows-right-left';
    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.inventory'); }
    public static function getModelLabel(): string { return __('resources.stock_movement.label'); }
    public static function getPluralModelLabel(): string { return __('resources.stock_movement.plural_label'); }
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false; // Stock movements are created via services only
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label(__('resources.fields.date')),
                Tables\Columns\TextColumn::make('item_type')->label(__('resources.fields.item_type'))->badge(),
                Tables\Columns\TextColumn::make('movement_type')->label(__('resources.fields.movement_type'))
                    ->badge()
                    ->color(fn (StockMovementType $state) => $state->color()),
                Tables\Columns\TextColumn::make('quantity')->label(__('resources.fields.quantity'))->numeric(decimalPlaces: 4),
                Tables\Columns\TextColumn::make('unit_cost')->label(__('resources.fields.unit_cost'))->numeric(decimalPlaces: 4),
                Tables\Columns\TextColumn::make('creator.name')->label(__('resources.fields.created_by')),
                Tables\Columns\TextColumn::make('notes')->label(__('resources.fields.notes'))->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('movement_type')->label(__('resources.fields.movement_type'))->options(StockMovementType::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'view'  => Pages\ViewStockMovement::route('/{record}'),
        ];
    }
}
