<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';
    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.system'); }
    public static function getModelLabel(): string { return __('resources.setting.label'); }
    public static function getPluralModelLabel(): string { return __('resources.setting.plural_label'); }
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make()->schema([
                Forms\Components\TextInput::make('key')->label(__('resources.fields.key'))->required()->unique(ignoreRecord: true)->maxLength(100),
                Forms\Components\TextInput::make('value')->label(__('resources.fields.value'))->required(),
                Forms\Components\Select::make('type')->label(__('resources.fields.type'))
                    ->options(['string' => 'String', 'boolean' => 'Boolean', 'integer' => 'Integer', 'json' => 'JSON'])
                    ->default('string')
                    ->required(),
                Forms\Components\TextInput::make('group')->label(__('resources.fields.group'))->default('general'),
            ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label(__('resources.fields.key'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('value')->label(__('resources.fields.value')),
                Tables\Columns\TextColumn::make('type')->label(__('resources.fields.type'))->badge(),
                Tables\Columns\TextColumn::make('group')->label(__('resources.fields.group'))->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')->label(__('resources.fields.group'))
                    ->options(fn () => Setting::distinct()->pluck('group', 'group')->toArray()),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
