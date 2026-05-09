<?php

namespace App\Filament\Resources;

use App\Enums\PurchaseOrderStatus;
use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use App\Services\PurchaseService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';
    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.purchasing'); }
    public static function getModelLabel(): string { return __('resources.purchase_order.label'); }
    public static function getPluralModelLabel(): string { return __('resources.purchase_order.plural_label'); }
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make(__('resources.sections.order_details'))->schema([
                Forms\Components\TextInput::make('number')->label(__('resources.fields.number'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'PO-' . str_pad(PurchaseOrder::max('id') + 1, 6, '0', STR_PAD_LEFT)),

                Forms\Components\Select::make('supplier_id')->label(__('resources.fields.supplier'))
                    ->relationship('supplier', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\DatePicker::make('order_date')->label(__('resources.fields.order_date'))
                    ->required()
                    ->default(now()),

                Forms\Components\DatePicker::make('expected_date')->label(__('resources.fields.expected_date')),

                Forms\Components\Select::make('status')->label(__('resources.fields.status'))
                    ->options(PurchaseOrderStatus::class)
                    ->default(PurchaseOrderStatus::Draft)
                    ->required()
                    ->disabled(fn (?PurchaseOrder $record) => $record !== null),

                Forms\Components\Textarea::make('notes')->label(__('resources.fields.notes'))->columnSpanFull(),
            ])->columns(2),

            Section::make(__('resources.sections.items'))->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('material_id')
                            ->relationship('material', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => app()->getLocale() === 'ar' ? ($record->name_ar ?: $record->name) : $record->name)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(3),

                        Forms\Components\TextInput::make('quantity')->label(__('resources.fields.quantity'))
                            ->numeric()
                            ->required()
                            ->minValue(0.0001)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('unit_price')->label(__('resources.fields.unit_price'))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->columnSpan(2),
                    ])
                    ->columns(7)
                    ->addActionLabel(__('resources.fields.add_material')),
            ])->columns(1),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label(__('resources.fields.number'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->searchable()->sortable()->label(__('resources.fields.supplier')),
                Tables\Columns\TextColumn::make('status')->label(__('resources.fields.status'))
                    ->badge()
                    ->color(fn (PurchaseOrderStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('order_date')->label(__('resources.fields.order_date'))->date()->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->label(__('resources.fields.total_amount'))->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('creator.name')->label(__('resources.fields.created_by')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('resources.fields.status'))->options(PurchaseOrderStatus::class),
                Tables\Filters\SelectFilter::make('supplier')->relationship('supplier', 'name'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('approve')
                    ->label(__('resources.fields.approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PurchaseOrder $record) => $record->status === PurchaseOrderStatus::Draft)
                    ->action(function (PurchaseOrder $record): void {
                        app(PurchaseService::class)->approvePurchaseOrder($record);
                        Notification::make()->title('Purchase order approved')->success()->send();
                    }),
                Action::make('receive')
                    ->label(__('resources.fields.receive_goods'))
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->color('primary')
                    ->visible(fn (PurchaseOrder $record) => in_array($record->status, [PurchaseOrderStatus::Approved, PurchaseOrderStatus::PartiallyReceived]))
                    ->form([
                        Forms\Components\Repeater::make('items')
                            ->label(__('resources.fields.items_to_receive'))
                            ->schema([
                                Forms\Components\Select::make('purchase_order_item_id')
                                    ->label(__('resources.fields.item'))
                                    ->options(fn (Forms\Get $get, $livewire) =>
                                        $livewire->record->items()
                                            ->with('material')
                                            ->get()
                                            ->mapWithKeys(fn ($i) => [$i->id => $i->material->name . ' (ordered: ' . $i->quantity . ')'])
                                    )
                                    ->required(),
                                Forms\Components\TextInput::make('quantity_received')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.0001),
                                Forms\Components\TextInput::make('unit_cost')->label(__('resources.fields.unit_cost'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),
                            ])
                            ->columns(3)
                            ->minItems(1),
                    ])
                    ->action(function (PurchaseOrder $record, array $data): void {
                        app(PurchaseService::class)->receiveGoods($record, $data['items']);
                        Notification::make()->title('Goods received successfully')->success()->send();
                    }),
                DeleteAction::make()
                    ->visible(fn (PurchaseOrder $record) => $record->status === PurchaseOrderStatus::Draft),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
