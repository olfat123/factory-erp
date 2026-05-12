<?php

namespace App\Filament\Resources\JournalEntryResource\Pages;

use App\Filament\Resources\JournalEntryResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewJournalEntry extends ViewRecord
{
    protected static string $resource = JournalEntryResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->schema([
            Section::make(__('resources.sections.journal_entry_details'))->schema([
                TextEntry::make('reference_number')
                    ->label(__('resources.fields.reference_number')),
                TextEntry::make('type')
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
                TextEntry::make('total_amount')
                    ->label(__('resources.fields.total_amount'))
                    ->numeric(2),
                TextEntry::make('posted_at')
                    ->label(__('resources.fields.posted_at'))
                    ->dateTime(),
                TextEntry::make('creator.name')
                    ->label(__('resources.fields.created_by')),
                TextEntry::make('description')
                    ->label(__('resources.fields.description'))
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make(__('resources.sections.journal_lines'))->schema([
                RepeatableEntry::make('lines')->schema([
                    TextEntry::make('account.code')
                        ->label(__('resources.fields.account_code')),
                    TextEntry::make('account.translated_name')
                        ->label(__('resources.fields.account_name')),
                    TextEntry::make('debit')
                        ->label(__('resources.fields.debit'))
                        ->numeric(2),
                    TextEntry::make('credit')
                        ->label(__('resources.fields.credit'))
                        ->numeric(2),
                    TextEntry::make('description')
                        ->label(__('resources.fields.description')),
                ])->columns(5),
            ]),
        ]);
    }
}

