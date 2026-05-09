<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoodsReceiptResource\Pages;
use App\Models\GoodsReceipt;
use App\Models\Material;
use App\Models\PurchaseOrder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

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
            Section::make(__('resources.sections.receipt_details'))->schema([
                TextInput::make('number')
                    ->label(__('resources.fields.number'))
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                Select::make('purchase_order_id')
                    ->label(__('resources.purchase_order.label'))
                    ->relationship('purchaseOrder', 'number')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('received_by')
                    ->label(__('resources.fields.received_by'))
                    ->relationship('receiver', 'name')
                    ->searchable()
                    ->preload(),

                DatePicker::make('received_date')
                    ->label(__('resources.fields.date'))
                    ->native(false)
                    ->default(today())
                    ->required(),

                Textarea::make('notes')
                    ->label(__('resources.fields.notes'))
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make(__('resources.sections.items'))->schema([
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Select::make('material_id')
                            ->label(__('resources.fields.item'))
                            ->options(Material::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('quantity')
                            ->label(__('resources.fields.quantity'))
                            ->numeric()
                            ->minValue(0.0001)
                            ->required(),

                        TextInput::make('unit_cost')
                            ->label(__('resources.fields.unit_price'))
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('batch_number')
                            ->label(__('resources.fields.batch'))
                            ->maxLength(100),

                        DatePicker::make('expiry_date')
                            ->label('Expiry Date')
                            ->native(false),
                    ])
                    ->columns(5)
                    ->reorderable(false)
                    ->addActionLabel(__('resources.fields.add_material')),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label(__('resources.fields.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purchaseOrder.number')
                    ->label(__('resources.fields.po_number'))
                    ->searchable(),
                TextColumn::make('purchaseOrder.supplier.name')
                    ->label(__('resources.fields.supplier')),
                TextColumn::make('receiver.name')
                    ->label(__('resources.fields.received_by')),
                TextColumn::make('received_date')
                    ->label(__('resources.fields.date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label(__('resources.fields.items_count')),
            ])
            ->filters([
                SelectFilter::make('purchase_order_id')
                    ->relationship('purchaseOrder', 'number')
                    ->label(__('resources.purchase_order.label')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('received_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGoodsReceipts::route('/'),
            'create' => Pages\CreateGoodsReceipt::route('/create'),
            'edit'   => Pages\EditGoodsReceipt::route('/{record}/edit'),
            'view'   => Pages\ViewGoodsReceipt::route('/{record}'),
        ];
    }
}
