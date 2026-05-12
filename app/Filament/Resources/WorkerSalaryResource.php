<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerSalaryResource\Pages;
use App\Models\Worker;
use App\Models\WorkerSalary;
use App\Services\SettingsService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class WorkerSalaryResource extends Resource
{
    protected static ?string $model = WorkerSalary::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.accounting');
    }

    public static function getModelLabel(): string
    {
        return __('resources.worker_salary.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.worker_salary.plural_label');
    }

    public static function form(Schema $form): Schema
    {
        $svc = app(SettingsService::class);
        $currency             = $svc->getSalaryCurrency();
        $defaultWorkingDays   = $svc->getWorkingDaysPerMonth();
        $defaultWorkingHours  = $svc->getWorkingHoursPerDay();
        $overtimeRateMulti    = $svc->getOvertimeRate();
        $socialRate           = $svc->getSocialInsuranceRate();
        $taxRate              = $svc->getTaxRate();

        $recalculate = function (Get $get, Set $set) use (
            $defaultWorkingDays, $defaultWorkingHours, $overtimeRateMulti, $socialRate, $taxRate
        ) {
            $workerId    = $get('worker_id');
            $workingDays = (float) ($get('working_days') ?: 0);
            $otHours     = (float) ($get('overtime_hours') ?: 0);
            $bonuses     = (float) ($get('bonuses') ?: 0);
            $deductions  = (float) ($get('deductions') ?: 0);

            if (!$workerId) return;

            $worker = Worker::find($workerId);
            if (!$worker) return;

            $baseSalary   = (float) $worker->base_salary;
            $dailyRate    = $defaultWorkingDays > 0 ? $baseSalary / $defaultWorkingDays : 0;
            $hourlyRate   = $defaultWorkingHours > 0 ? $dailyRate / $defaultWorkingHours : 0;
            $otPay        = $hourlyRate * $overtimeRateMulti * $otHours;

            $gross          = ($dailyRate * $workingDays) + $otPay + $bonuses - $deductions;
            $gross          = max(0, $gross);
            $socialAmount   = round($gross * ($socialRate / 100), 2);
            $taxAmount      = round($gross * ($taxRate / 100), 2);
            $net            = round($gross - $socialAmount - $taxAmount, 2);

            $set('gross_salary',     round($gross, 2));
            $set('social_insurance', $socialAmount);
            $set('tax',              $taxAmount);
            $set('net_salary',       $net);
        };

        return $form->schema([
            Section::make(__('resources.worker_salary.section_details'))->schema([
                Select::make('worker_id')
                    ->label(__('resources.worker.label'))
                    ->options(Worker::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated($recalculate),

                DatePicker::make('period')
                    ->label(__('resources.worker_salary.period'))
                    ->native(false)
                    ->displayFormat('Y-m')
                    ->required(),
            ])->columns(2),

            Section::make(__('resources.worker_salary.section_attendance'))->schema([
                TextInput::make('working_days')
                    ->label(__('resources.worker_salary.working_days'))
                    ->numeric()
                    ->default($defaultWorkingDays)
                    ->minValue(0)
                    ->maxValue(31)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated($recalculate),

                TextInput::make('overtime_hours')
                    ->label(__('resources.worker_salary.overtime_hours'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->reactive()
                    ->afterStateUpdated($recalculate),

                TextInput::make('bonuses')
                    ->label(__('resources.worker_salary.bonuses'))
                    ->suffix($currency)
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->reactive()
                    ->afterStateUpdated($recalculate),

                TextInput::make('deductions')
                    ->label(__('resources.worker_salary.deductions'))
                    ->suffix($currency)
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->reactive()
                    ->afterStateUpdated($recalculate),
            ])->columns(2),

            Section::make(__('resources.worker_salary.section_calculation'))->schema([
                TextInput::make('gross_salary')
                    ->label(__('resources.worker_salary.gross_salary'))
                    ->suffix($currency)
                    ->numeric()
                    ->readOnly(),

                TextInput::make('social_insurance')
                    ->label(__('resources.worker_salary.social_insurance'))
                    ->suffix($currency)
                    ->numeric()
                    ->readOnly(),

                TextInput::make('tax')
                    ->label(__('resources.worker_salary.tax'))
                    ->suffix($currency)
                    ->numeric()
                    ->readOnly(),

                TextInput::make('net_salary')
                    ->label(__('resources.worker_salary.net_salary'))
                    ->suffix($currency)
                    ->numeric()
                    ->readOnly(),
            ])->columns(2),

            Section::make()->schema([
                Textarea::make('notes')
                    ->label(__('resources.fields.notes'))
                    ->columnSpanFull(),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        $svc      = app(SettingsService::class);
        $currency = $svc->getSalaryCurrency();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('worker.name')
                    ->label(__('resources.worker.label'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label(__('resources.worker_salary.period'))
                    ->date('Y-m')
                    ->sortable(),

                Tables\Columns\TextColumn::make('working_days')
                    ->label(__('resources.worker_salary.working_days'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('gross_salary')
                    ->label(__('resources.worker_salary.gross_salary'))
                    ->suffix(' ' . $currency)
                    ->numeric(2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_salary')
                    ->label(__('resources.worker_salary.net_salary'))
                    ->suffix(' ' . $currency)
                    ->numeric(2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('worker_id')
                    ->label(__('resources.worker.label'))
                    ->relationship('worker', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('period', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkerSalaries::route('/'),
            'create' => Pages\CreateWorkerSalary::route('/create'),
            'edit'   => Pages\EditWorkerSalary::route('/{record}/edit'),
        ];
    }
}
