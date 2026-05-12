<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Filament\Resources\StockMovementResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewStockMovement extends ViewRecord
{
    protected static string $resource = StockMovementResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->schema([
            Section::make()->schema([
                TextEntry::make('created_at')
                    ->label(__('resources.fields.date'))
                    ->dateTime(),

                TextEntry::make('movement_type')
                    ->label(__('resources.fields.movement_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\StockMovementType ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof \App\Enums\StockMovementType ? $state->color() : 'gray'),

                TextEntry::make('item_type')
                    ->label(__('resources.fields.item_type')),

                TextEntry::make('quantity')
                    ->label(__('resources.fields.quantity'))
                    ->numeric(4),

                TextEntry::make('unit_cost')
                    ->label(__('resources.fields.unit_cost'))
                    ->numeric(4),

                TextEntry::make('creator.name')
                    ->label(__('resources.fields.created_by')),

                TextEntry::make('notes')
                    ->label(__('resources.fields.notes'))
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
