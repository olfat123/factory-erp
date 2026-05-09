<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-archive-box';
    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.manufacturing'); }
    public static function getModelLabel(): string { return __('resources.product.label'); }
    public static function getPluralModelLabel(): string { return __('resources.product.plural_label'); }
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make(__('resources.sections.product_details'))->schema([
                Forms\Components\TextInput::make('sku')->label(__('resources.fields.sku'))->required()->unique(ignoreRecord: true)->maxLength(100),
                Forms\Components\TextInput::make('name')->label(__('resources.fields.name'))->required()->maxLength(255),
                Forms\Components\TextInput::make('name_ar')->label(__('resources.fields.name_ar'))->maxLength(255),
                Forms\Components\Textarea::make('description')->label(__('resources.fields.description'))->columnSpanFull(),
                Forms\Components\TextInput::make('production_time')->label(__('resources.fields.production_time'))->numeric()->default(0)->suffix('min'),
                Forms\Components\Toggle::make('is_active')->label(__('resources.fields.is_active'))->default(true),
            ])->columns(2),

            \Filament\Schemas\Components\Section::make(__('resources.sections.bill_of_materials'))->schema([
                Forms\Components\Repeater::make('bomItems')
                    ->label(__('resources.pages.bom_items'))
                    ->schema([
                        Forms\Components\Select::make('material_id')->label(__('resources.fields.item'))
                            ->options(fn () => \App\Models\Material::query()->where('is_active', true)->get()->mapWithKeys(fn ($m) => [$m->id => app()->getLocale() === 'ar' ? ($m->name_ar ?: $m->name) : $m->name]))
                            ->required()
                            ->searchable()
                            ->columnSpan(4),
                        Forms\Components\TextInput::make('quantity')->label(__('resources.fields.quantity'))
                            ->numeric()
                            ->required()
                            ->minValue(0.0001)
                            ->columnSpan(2),
                        Forms\Components\Select::make('unit_id')->label(__('resources.fields.unit'))
                            ->options(fn () => \App\Models\Unit::query()->get()->mapWithKeys(fn ($u) => [$u->id => app()->getLocale() === 'ar' ? ($u->name_ar ?: $u->name) : $u->name]))
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(8)
                    ->addActionLabel(__('resources.fields.add_material')),
            ])->columns(1),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->label(__('resources.fields.sku'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label(__('resources.fields.name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('current_stock')->label(__('resources.fields.current_stock'))->numeric(decimalPlaces: 2)->sortable(),
                Tables\Columns\TextColumn::make('production_time')->label(__('resources.fields.production_time'))->suffix(' min'),
                Tables\Columns\IconColumn::make('is_active')->label(__('resources.fields.is_active'))->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(__('resources.fields.is_active')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
