<?php

namespace App\Filament\Resources;

use App\Enums\ProductionOrderStatus;
use App\Filament\Resources\ProductionOrderResource\Pages;
use App\Models\ProductionOrder;
use App\Services\ProductionService;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class ProductionOrderResource extends Resource
{
    protected static ?string $model = ProductionOrder::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-beaker';
    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.manufacturing'); }
    public static function getModelLabel(): string { return __('resources.production_order.label'); }
    public static function getPluralModelLabel(): string { return __('resources.production_order.plural_label'); }
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make()->schema([
                Forms\Components\TextInput::make('number')->label(__('resources.fields.number'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'PO-' . str_pad(ProductionOrder::max('id') + 1, 6, '0', STR_PAD_LEFT)),

                Forms\Components\Select::make('product_id')->label(__('resources.fields.product'))
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('quantity')->label(__('resources.fields.quantity'))
                    ->numeric()
                    ->required()
                    ->minValue(0.0001),

                Forms\Components\DatePicker::make('planned_date')->label(__('resources.fields.planned_date')),

                Forms\Components\Select::make('status')->label(__('resources.fields.status'))
                    ->options(ProductionOrderStatus::class)
                    ->default(ProductionOrderStatus::Draft)
                    ->required()
                    ->disabled(fn (?ProductionOrder $record) => $record !== null),

                Forms\Components\Textarea::make('notes')->label(__('resources.fields.notes'))->columnSpanFull(),
            ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label(__('resources.fields.number'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('product.name')->label(__('resources.fields.product'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label(__('resources.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (ProductionOrderStatus $state) => $state->getLabel())
                    ->color(fn (ProductionOrderStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('quantity')->label(__('resources.fields.quantity'))->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('completed_quantity')->label(__('resources.fields.actual_quantity'))->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('planned_date')->label(__('resources.fields.planned_date'))->date()->sortable(),
                Tables\Columns\TextColumn::make('creator.name')->label(__('resources.fields.created_by')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('resources.fields.status'))->options(ProductionOrderStatus::class),
                Tables\Filters\SelectFilter::make('product')->relationship('product', 'name'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('approve')
                    ->label(__('resources.fields.approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ProductionOrder $record) => $record->status === ProductionOrderStatus::Draft)
                    ->action(function (ProductionOrder $record): void {
                        app(ProductionService::class)->approveOrder($record);
                        Notification::make()->title('Order approved')->success()->send();
                    }),
                Action::make('start')
                    ->label(__('resources.fields.start_production'))
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->visible(fn (ProductionOrder $record) => $record->status === ProductionOrderStatus::Approved)
                    ->action(function (ProductionOrder $record): void {
                        app(ProductionService::class)->startProduction($record);
                        Notification::make()->title('Production started')->success()->send();
                    }),
                Action::make('complete')
                    ->label(__('resources.actions.complete'))
                    ->icon('heroicon-o-flag')
                    ->color('primary')
                    ->visible(fn (ProductionOrder $record) => $record->status === ProductionOrderStatus::InProduction)
                    ->form([
                        Forms\Components\TextInput::make('actual_quantity')->label(__('resources.fields.actual_qty_produced'))
                            ->numeric()
                            ->required()
                            ->minValue(0.0001),
                    ])
                    ->action(function (ProductionOrder $record, array $data): void {
                        app(ProductionService::class)->completeProduction($record, $data['actual_quantity']);
                        Notification::make()->title('Production completed')->success()->send();
                    }),
                DeleteAction::make()
                    ->visible(fn (ProductionOrder $record) => $record->status === ProductionOrderStatus::Draft),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductionOrders::route('/'),
            'create' => Pages\CreateProductionOrder::route('/create'),
            'edit' => Pages\EditProductionOrder::route('/{record}/edit'),
        ];
    }
}
