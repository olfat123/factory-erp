<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Models\Unit;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-scale';

    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.inventory'); }
    public static function getModelLabel(): string { return __('resources.unit.label'); }
    public static function getPluralModelLabel(): string { return __('resources.unit.plural_label'); }

    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            TextInput::make('name')->label(__('resources.fields.name'))->required()->maxLength(50),
            TextInput::make('abbreviation')->label(__('resources.fields.abbreviation'))->required()->maxLength(10),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('resources.fields.name'))
                    ->formatStateUsing(fn ($record) => app()->getLocale() === 'ar' ? ($record->name_ar ?: $record->name) : $record->name)
                    ->searchable()->sortable(),
                TextColumn::make('abbreviation')->label(__('resources.fields.abbreviation'))->sortable(),
                TextColumn::make('materials_count')->counts('materials')->label(__('resources.fields.materials_count')),
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
            'index'  => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit'   => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
