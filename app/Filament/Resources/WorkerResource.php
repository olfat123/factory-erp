<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Models\Worker;
use App\Services\SettingsService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.accounting');
    }

    public static function getModelLabel(): string
    {
        return __('resources.worker.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.worker.plural_label');
    }

    public static function form(Schema $form): Schema
    {
        $svc = app(SettingsService::class);
        $currency = $svc->getSalaryCurrency();

        return $form->schema([
            Section::make(__('resources.worker.details_section'))->schema([
                TextInput::make('name')
                    ->label(__('resources.fields.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('name_ar')
                    ->label(__('resources.fields.name_ar'))
                    ->maxLength(255),

                TextInput::make('job_title')
                    ->label(__('resources.worker.job_title'))
                    ->maxLength(255),

                TextInput::make('base_salary')
                    ->label(__('resources.worker.base_salary'))
                    ->suffix($currency)
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                DatePicker::make('hire_date')
                    ->label(__('resources.worker.hire_date'))
                    ->native(false),

                Toggle::make('is_active')
                    ->label(__('resources.fields.is_active'))
                    ->default(true),

                Textarea::make('notes')
                    ->label(__('resources.fields.notes'))
                    ->columnSpanFull(),
            ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        $svc = app(SettingsService::class);
        $currency = $svc->getSalaryCurrency();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('resources.fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('job_title')
                    ->label(__('resources.worker.job_title'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('base_salary')
                    ->label(__('resources.worker.base_salary'))
                    ->suffix(' ' . $currency)
                    ->numeric(2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('hire_date')
                    ->label(__('resources.worker.hire_date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('resources.fields.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('resources.fields.is_active')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkers::route('/'),
            'create' => Pages\CreateWorker::route('/create'),
            'edit'   => Pages\EditWorker::route('/{record}/edit'),
        ];
    }
}
