<?php

namespace App\Filament\Resources;

use App\Enums\MachineStatus;
use App\Filament\Resources\MachineResource\Pages;
use App\Models\Machine;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class MachineResource extends Resource
{
    protected static ?string $model = Machine::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.manufacturing'); }
    public static function getModelLabel(): string { return __('resources.machine.label'); }
    public static function getPluralModelLabel(): string { return __('resources.machine.plural_label'); }
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make()->schema([
                Forms\Components\TextInput::make('code')->label(__('resources.fields.code'))->required()->unique(ignoreRecord: true)->maxLength(50),
                Forms\Components\TextInput::make('name')->label(__('resources.fields.name'))->required()->maxLength(255),
                Forms\Components\TextInput::make('name_ar')->label(__('resources.fields.name_ar'))->maxLength(255),
                Forms\Components\Select::make('status')->label(__('resources.fields.status'))
                    ->options(MachineStatus::class)
                    ->default(MachineStatus::Available)
                    ->required(),
                Forms\Components\Textarea::make('notes')->label(__('resources.fields.notes'))->columnSpanFull(),
            ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(__('resources.fields.code'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label(__('resources.fields.name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label(__('resources.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (MachineStatus $state) => $state->getLabel())
                    ->color(fn (MachineStatus $state) => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('resources.fields.status'))->options(MachineStatus::class),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMachines::route('/'),
            'create' => Pages\CreateMachine::route('/create'),
            'edit' => Pages\EditMachine::route('/{record}/edit'),
        ];
    }
}
