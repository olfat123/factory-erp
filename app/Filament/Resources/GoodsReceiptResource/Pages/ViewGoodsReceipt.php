<?php

namespace App\Filament\Resources\GoodsReceiptResource\Pages;

use App\Filament\Resources\GoodsReceiptResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewGoodsReceipt extends ViewRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->schema([
            Section::make(__('resources.sections.receipt_details'))->schema([
                TextEntry::make('receipt_number'),
                TextEntry::make('purchaseOrder.number')->label(__('resources.fields.po_number')),
                TextEntry::make('purchaseOrder.supplier.name')->label(__('resources.fields.supplier')),
                TextEntry::make('receiver.name')->label(__('resources.fields.received_by')),
                TextEntry::make('received_at')->dateTime(),
                TextEntry::make('notes')->label(__('resources.fields.notes'))->columnSpanFull(),
            ])->columns(1),

            Section::make(__('resources.sections.items'))->schema([
                RepeatableEntry::make('items')->schema([
                    TextEntry::make('material.name')->label(__('resources.fields.item')),
                    TextEntry::make('quantity_received')->label(__('resources.fields.received_quantity')),
                    TextEntry::make('unit_cost')->label(__('resources.fields.unit_cost'))->money('USD'),
                    TextEntry::make('materialBatch.batch_number')->label(__('resources.fields.batch')),
                ])->columns(4),
            ]),
        ]);
    }
}
