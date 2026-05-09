<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.manage-settings';

    public static function getNavigationGroup(): ?string { return __('resources.nav.groups.system'); }
    public static function getNavigationLabel(): string { return __('resources.pages.settings'); }
    public function getTitle(): string { return __('resources.pages.settings'); }
    protected static ?int $navigationSort = 99;

    // General
    public ?string $language = null;

    // Security
    public bool $two_factor_enabled = false;

    // Approvals
    public bool $production_approval_enabled = true;
    public bool $purchase_approval_enabled   = true;

    // Salaries
    public ?string $salary_currency         = null;
    public int|string $working_days_per_month = 22;
    public int|string $working_hours_per_day  = 8;
    public float|string $overtime_rate         = 1.5;
    public float|string $social_insurance_rate = 0.0;
    public float|string $tax_rate              = 0.0;

    public function mount(): void
    {
        $svc = app(SettingsService::class);

        $this->language                   = $svc->getDefaultLanguage();
        $this->two_factor_enabled         = $svc->isTwoFactorEnabled();
        $this->production_approval_enabled = $svc->isProductionApprovalEnabled();
        $this->purchase_approval_enabled   = $svc->isPurchaseApprovalEnabled();
        $this->salary_currency            = (string) $svc->get('salary_currency', 'SAR');
        $this->working_days_per_month     = (int)    $svc->get('working_days_per_month', 22);
        $this->working_hours_per_day      = (int)    $svc->get('working_hours_per_day', 8);
        $this->overtime_rate              = (float)  $svc->get('overtime_rate', 1.5);
        $this->social_insurance_rate      = (float)  $svc->get('social_insurance_rate', 0.0);
        $this->tax_rate                   = (float)  $svc->get('tax_rate', 0.0);
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([

            Section::make(__('resources.settings.sections.general'))
                ->icon('heroicon-o-globe-alt')
                ->schema([
                    Select::make('language')
                        ->label(__('resources.pages.default_language'))
                        ->options(['en' => 'English', 'ar' => 'العربية'])
                        ->required(),
                ]),

            Section::make(__('resources.settings.sections.security'))
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Toggle::make('two_factor_enabled')
                        ->label(__('resources.pages.two_factor_enabled'))
                        ->helperText(__('resources.pages.two_factor_helper'))
                        ->onColor('success')
                        ->offColor('danger'),
                ]),

            Section::make(__('resources.settings.sections.approvals'))
                ->icon('heroicon-o-check-badge')
                ->schema([
                    Toggle::make('production_approval_enabled')
                        ->label(__('resources.settings.production_approval'))
                        ->helperText(__('resources.settings.production_approval_helper'))
                        ->onColor('success')
                        ->offColor('danger'),
                    Toggle::make('purchase_approval_enabled')
                        ->label(__('resources.settings.purchase_approval'))
                        ->helperText(__('resources.settings.purchase_approval_helper'))
                        ->onColor('success')
                        ->offColor('danger'),
                ]),

            Section::make(__('resources.settings.sections.salaries'))
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Select::make('salary_currency')
                        ->label(__('resources.settings.salary_currency'))
                        ->options([
                            'SAR' => 'SAR — Saudi Riyal',
                            'EGP' => 'EGP — Egyptian Pound',
                            'AED' => 'AED — UAE Dirham',
                            'USD' => 'USD — US Dollar',
                            'EUR' => 'EUR — Euro',
                        ])
                        ->required(),
                    TextInput::make('working_days_per_month')
                        ->label(__('resources.settings.working_days_per_month'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(31)
                        ->required(),
                    TextInput::make('working_hours_per_day')
                        ->label(__('resources.settings.working_hours_per_day'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(24)
                        ->required(),
                    TextInput::make('overtime_rate')
                        ->label(__('resources.settings.overtime_rate'))
                        ->helperText(__('resources.settings.overtime_rate_helper'))
                        ->numeric()
                        ->minValue(1)
                        ->step(0.1)
                        ->required(),
                    TextInput::make('social_insurance_rate')
                        ->label(__('resources.settings.social_insurance_rate'))
                        ->helperText(__('resources.settings.rate_percent_helper'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->suffix('%'),
                    TextInput::make('tax_rate')
                        ->label(__('resources.settings.tax_rate'))
                        ->helperText(__('resources.settings.rate_percent_helper'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->suffix('%'),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $svc  = app(SettingsService::class);

        $svc->set('default_language',            $data['language'],                                         'string',  'general');
        $svc->set('two_factor_enabled',          $data['two_factor_enabled']          ? 'true' : 'false',  'boolean', 'security');
        $svc->set('production_approval_enabled', $data['production_approval_enabled'] ? 'true' : 'false',  'boolean', 'production');
        $svc->set('purchase_approval_enabled',   $data['purchase_approval_enabled']   ? 'true' : 'false',  'boolean', 'purchasing');
        $svc->set('salary_currency',             $data['salary_currency'],                                  'string',  'salaries');
        $svc->set('working_days_per_month',      (string) $data['working_days_per_month'],                  'integer', 'salaries');
        $svc->set('working_hours_per_day',       (string) $data['working_hours_per_day'],                   'integer', 'salaries');
        $svc->set('overtime_rate',               (string) $data['overtime_rate'],                           'string',  'salaries');
        $svc->set('social_insurance_rate',       (string) $data['social_insurance_rate'],                   'string',  'salaries');
        $svc->set('tax_rate',                    (string) $data['tax_rate'],                                'string',  'salaries');

        Notification::make()->title(__('resources.pages.settings_saved'))->success()->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(fn () => __('resources.pages.save_settings'))
                ->submit('save'),
        ];
    }
}
