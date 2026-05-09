<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Models\JournalEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables;
use Filament\Tables\Table;

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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
            'view'  => Pages\ViewJournalEntry::route('/{record}'),
        ];
    }
}
