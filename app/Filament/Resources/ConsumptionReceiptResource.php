<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumptionReceiptResource\Pages;
use App\Models\ConsumptionReceipt;
use App\Services\SettingsService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ConsumptionReceiptResource extends Resource
{
    protected static ?string $model = ConsumptionReceipt::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.accounting');
    }

    public static function getModelLabel(): string
    {
        return __('resources.consumption_receipt.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.consumption_receipt.plural_label');
    }

    public static function form(Schema $form): Schema
    {
        $svc      = app(SettingsService::class);
        $currency = $svc->getSalaryCurrency();

        return $form->schema([
            Section::make(__('resources.consumption_receipt.section_details'))->schema([
                Select::make('type')
                    ->label(__('resources.fields.type'))
                    ->options(ConsumptionReceipt::types())
                    ->required(),

                TextInput::make('description')
                    ->label(__('resources.fields.description'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('amount')
                    ->label(__('resources.consumption_receipt.amount'))
                    ->suffix($currency)
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                TextInput::make('reference_number')
                    ->label(__('resources.fields.reference_number'))
                    ->maxLength(100),

                DatePicker::make('receipt_date')
                    ->label(__('resources.consumption_receipt.receipt_date'))
                    ->native(false)
                    ->required(),

                DatePicker::make('period_month')
                    ->label(__('resources.consumption_receipt.period_month'))
                    ->native(false)
                    ->displayFormat('Y-m')
                    ->helperText(__('resources.consumption_receipt.period_month_helper'))
                    ->required(),

                Textarea::make('notes')
                    ->label(__('resources.fields.notes'))
                    ->columnSpanFull(),
            ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        $svc      = app(SettingsService::class);
        $currency = $svc->getSalaryCurrency();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('resources.fields.type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => ConsumptionReceipt::types()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'electricity'         => 'warning',
                        'telephone'           => 'info',
                        'internet'            => 'primary',
                        'machine_maintenance' => 'danger',
                        'rent'                => 'success',
                        default               => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('resources.fields.description'))
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('resources.consumption_receipt.amount'))
                    ->suffix(' ' . $currency)
                    ->numeric(2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('receipt_date')
                    ->label(__('resources.consumption_receipt.receipt_date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_month')
                    ->label(__('resources.consumption_receipt.period_month'))
                    ->date('Y-m')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference_number')
                    ->label(__('resources.fields.reference_number'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('resources.fields.type'))
                    ->options(ConsumptionReceipt::types()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('period_month', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConsumptionReceipts::route('/'),
            'create' => Pages\CreateConsumptionReceipt::route('/create'),
            'edit'   => Pages\EditConsumptionReceipt::route('/{record}/edit'),
        ];
    }
}
