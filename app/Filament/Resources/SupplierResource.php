<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';
    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.purchasing'); }
    public static function getModelLabel(): string { return __('resources.supplier.label'); }
    public static function getPluralModelLabel(): string { return __('resources.supplier.plural_label'); }
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')->label(__('resources.fields.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')->label(__('resources.fields.phone'))
                    ->tel()
                    ->maxLength(50),

                Forms\Components\TextInput::make('email')->label(__('resources.fields.email'))
                    ->email()
                    ->maxLength(255),

                Forms\Components\Textarea::make('address')->label(__('resources.fields.address'))
                    ->maxLength(500)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('tax_number')
                    ->maxLength(100),

                Forms\Components\Toggle::make('is_active')->label(__('resources.fields.is_active'))
                    ->default(true),
            ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('resources.fields.name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->label(__('resources.fields.phone')),
                Tables\Columns\TextColumn::make('email')->label(__('resources.fields.email'))->searchable(),
                Tables\Columns\TextColumn::make('tax_number'),
                Tables\Columns\IconColumn::make('is_active')->label(__('resources.fields.is_active'))->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(__('resources.fields.is_active')),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
