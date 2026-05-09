<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialCategoryResource\Pages;
use App\Models\MaterialCategory;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialCategoryResource extends Resource
{
    protected static ?string $model = MaterialCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.inventory'); }
    public static function getModelLabel(): string { return __('resources.material_category.label'); }
    public static function getPluralModelLabel(): string { return __('resources.material_category.plural_label'); }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            TextInput::make('name')->label(__('resources.fields.name'))->required()->maxLength(100),
            Textarea::make('description')->label(__('resources.fields.description'))->columnSpanFull(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('resources.fields.name'))
                    ->formatStateUsing(fn ($record) => app()->getLocale() === 'ar' ? ($record->name_ar ?: $record->name) : $record->name)
                    ->searchable()->sortable(),
                TextColumn::make('materials_count')->counts('materials')->label('Materials'),
                TextColumn::make('description')->label(__('resources.fields.description'))->limit(60),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaterialCategories::route('/'),
            'create' => Pages\CreateMaterialCategory::route('/create'),
            'edit'   => Pages\EditMaterialCategory::route('/{record}/edit'),
        ];
    }
}
