<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';
    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.inventory'); }
    public static function getModelLabel(): string { return __('resources.material.label'); }
    public static function getPluralModelLabel(): string { return __('resources.material.plural_label'); }
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('resources.material.plural_label');
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make()->schema([
                Forms\Components\TextInput::make('code')->label(__('resources.fields.code'))
                    ->label(__('materials.code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                Forms\Components\TextInput::make('name')->label(__('resources.fields.name'))
                    ->label(__('materials.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('name_ar')->label(__('resources.fields.name_ar'))
                    ->label(__('resources.fields.name_ar'))
                    ->maxLength(255),

                Forms\Components\Select::make('category_id')->label(__('resources.fields.category'))
                    ->label(__('materials.category'))
                    ->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => app()->getLocale() === 'ar' ? ($record->name_ar ?: $record->name) : $record->name)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->label(__('resources.fields.name'))->required(),
                        Forms\Components\TextInput::make('name_ar')->label(__('resources.fields.name_ar'))->required(),
                        Forms\Components\TextInput::make('slug')->label(__('resources.fields.slug'))->required(),
                    ]),

                Forms\Components\Select::make('unit_id')->label(__('resources.fields.unit'))
                    ->label(__('materials.unit'))
                    ->relationship('unit', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => app()->getLocale() === 'ar' ? ($record->name_ar ?: $record->name) : $record->name)
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('current_stock')->label(__('resources.fields.current_stock'))
                    ->label(__('materials.current_stock'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\TextInput::make('minimum_stock')->label(__('resources.fields.minimum_stock'))
                    ->label(__('materials.minimum_stock'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\TextInput::make('average_cost')->label(__('resources.fields.average_cost'))
                    ->label(__('materials.average_cost'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\TextInput::make('market_cost')
                    ->label(__('resources.fields.market_cost'))
                    ->numeric()
                    ->minValue(0),

                Forms\Components\Toggle::make('is_active')->label(__('resources.fields.is_active'))
                    ->label(__('materials.is_active'))
                    ->default(true),
            ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(__('resources.fields.code'))
                    ->label(__('materials.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('translated_name')->label(__('resources.fields.name'))
                    ->label(__('materials.name'))
                    ->searchable(['name', 'name_ar'])
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('name', $direction)),

                Tables\Columns\TextColumn::make('category.translated_name')
                    ->label(__('materials.category'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.symbol')
                    ->label(__('materials.unit')),

                Tables\Columns\TextColumn::make('current_stock')->label(__('resources.fields.current_stock'))
                    ->label(__('materials.current_stock'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->color(fn (Material $record) => $record->isLowStock() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('minimum_stock')->label(__('resources.fields.minimum_stock'))
                    ->label(__('materials.minimum_stock'))
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\IconColumn::make('is_active')->label(__('resources.fields.is_active'))
                    ->label(__('materials.is_active'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => app()->getLocale() === 'ar' ? ($record->name_ar ?: $record->name) : $record->name),
                Tables\Filters\TernaryFilter::make('is_active')->label(__('resources.fields.is_active'))
                    ->label(__('materials.is_active')),
                Tables\Filters\Filter::make('low_stock')
                    ->label(__('materials.low_stock_alert'))
                    ->query(fn ($query) => $query->whereColumn('current_stock', '<=', 'minimum_stock')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
