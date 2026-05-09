<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoodsReceiptResource\Pages;
use App\Models\GoodsReceipt;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GoodsReceiptResource extends Resource
{
    protected static ?string $model = GoodsReceipt::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.purchasing'); }
    public static function getModelLabel(): string { return __('resources.goods_receipt.label'); }
    public static function getPluralModelLabel(): string { return __('resources.goods_receipt.plural_label'); }

    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('purchase_order_id')
                ->relationship('purchaseOrder', 'number')
                ->required()
                ->searchable(),

            TextInput::make('receipt_number')
                ->required()
                ->maxLength(50),

            DateTimePicker::make('received_at')
                ->required()
                ->default(now()),

            Textarea::make('notes')->label(__('resources.fields.notes'))
                ->columnSpanFull(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_number')->searchable()->sortable(),
                TextColumn::make('purchaseOrder.number')->label(__('resources.fields.po_number'))->searchable(),
                TextColumn::make('purchaseOrder.supplier.name')->label(__('resources.fields.supplier')),
                TextColumn::make('receiver.name')->label(__('resources.fields.received_by')),
                TextColumn::make('received_at')->dateTime()->sortable(),
                TextColumn::make('items_count')->counts('items')->label(__('resources.fields.items_count')),
            ])
            ->filters([
                SelectFilter::make('purchase_order_id')
                    ->relationship('purchaseOrder', 'number')
                    ->label('Purchase Order'),
            ])
            ->defaultSort('received_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoodsReceipts::route('/'),
            'view'  => Pages\ViewGoodsReceipt::route('/{record}'),
        ];
    }
}
