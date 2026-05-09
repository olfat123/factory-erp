<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Models\Account;
use App\Models\JournalEntry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
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
use Filament\Actions\ViewAction;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.accounting');
    }

    public static function getModelLabel(): string
    {
        return __('resources.journal_entry.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.journal_entry.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make(__('resources.sections.journal_entry_details'))->schema([
                TextInput::make('reference_number')
                    ->label(__('resources.fields.reference_number'))
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'JE-' . now()->format('Ymd') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT)),

                Select::make('type')
                    ->label(__('resources.fields.entry_type'))
                    ->options([
                        'adjustment_increase' => __('resources.journal_types.adjustment_increase'),
                        'adjustment_decrease' => __('resources.journal_types.adjustment_decrease'),
                    ])
                    ->required(),

                DateTimePicker::make('posted_at')
                    ->label(__('resources.fields.posted_at'))
                    ->native(false)
                    ->default(now())
                    ->required(),

                TextInput::make('description')
                    ->label(__('resources.fields.description'))
                    ->maxLength(255)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make(__('resources.sections.journal_lines'))->schema([
                Repeater::make('lines')
                    ->relationship()
                    ->schema([
                        Select::make('account_id')
                            ->label(__('resources.fields.account_name'))
                            ->options(Account::where('is_active', true)->orderBy('code')->get()->mapWithKeys(
                                fn ($a) => [$a->id => "{$a->code} — {$a->translated_name}"]
                            ))
                            ->searchable()
                            ->required(),

                        TextInput::make('debit')
                            ->label(__('resources.fields.debit'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        TextInput::make('credit')
                            ->label(__('resources.fields.credit'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        TextInput::make('description')
                            ->label(__('resources.fields.description'))
                            ->maxLength(255),
                    ])
                    ->columns(4)
                    ->minItems(2)
                    ->reorderable(false)
                    ->addActionLabel(__('resources.fields.add_material')),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label(__('resources.fields.reference_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('resources.fields.entry_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __('resources.journal_types.' . $state))
                    ->color(fn (string $state) => match ($state) {
                        'goods_received'       => 'success',
                        'production_consume'   => 'warning',
                        'production_output'    => 'info',
                        'adjustment_increase'  => 'success',
                        'adjustment_decrease'  => 'danger',
                        default                => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('resources.fields.total_amount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('resources.fields.description'))
                    ->limit(60),
                Tables\Columns\TextColumn::make('posted_at')
                    ->label(__('resources.fields.posted_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('resources.fields.created_by')),
            ])
            ->defaultSort('posted_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('resources.fields.entry_type'))
                    ->options(fn () => [
                        'goods_received'       => __('resources.journal_types.goods_received'),
                        'production_consume'   => __('resources.journal_types.production_consume'),
                        'production_output'    => __('resources.journal_types.production_output'),
                        'adjustment_increase'  => __('resources.journal_types.adjustment_increase'),
                        'adjustment_decrease'  => __('resources.journal_types.adjustment_decrease'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index'  => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'edit'   => Pages\EditJournalEntry::route('/{record}/edit'),
            'view'   => Pages\ViewJournalEntry::route('/{record}'),
        ];
    }
}
