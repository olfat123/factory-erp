<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.accounting');
    }

    public static function getModelLabel(): string
    {
        return __('resources.account.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.account.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('code')
                ->label(__('resources.fields.account_code'))
                ->required()
                ->maxLength(20)
                ->unique(ignoreRecord: true),
            TextInput::make('name')
                ->label(__('resources.fields.account_name'))
                ->required(),
            TextInput::make('name_ar')
                ->label(__('resources.fields.account_name_ar')),
            Select::make('type')
                ->label(__('resources.fields.account_type'))
                ->required()
                ->options([
                    'asset'     => __('resources.account_types.asset'),
                    'liability' => __('resources.account_types.liability'),
                    'equity'    => __('resources.account_types.equity'),
                    'revenue'   => __('resources.account_types.revenue'),
                    'expense'   => __('resources.account_types.expense'),
                ]),
            Toggle::make('is_active')
                ->label(__('resources.fields.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('resources.fields.account_code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('translated_name')
                    ->label(__('resources.fields.account_name'))
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'like', "%{$search}%")->orWhere('name_ar', 'like', "%{$search}%")),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('resources.fields.account_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __('resources.account_types.' . $state))
                    ->color(fn (string $state) => match ($state) {
                        'asset'     => 'success',
                        'liability' => 'danger',
                        'equity'    => 'info',
                        'revenue'   => 'success',
                        'expense'   => 'warning',
                        default     => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('resources.fields.is_active'))
                    ->boolean(),
            ])
            ->defaultSort('code')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('resources.fields.account_type'))
                    ->options([
                        'asset'     => __('resources.account_types.asset'),
                        'liability' => __('resources.account_types.liability'),
                        'equity'    => __('resources.account_types.equity'),
                        'revenue'   => __('resources.account_types.revenue'),
                        'expense'   => __('resources.account_types.expense'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit'   => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
