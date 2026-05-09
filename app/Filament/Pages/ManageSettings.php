<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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
    public static function getNavigationLabel(): string { return __('resources.pages.language_settings'); }
    public function getTitle(): string { return __('resources.pages.language_settings'); }
    protected static ?int $navigationSort = 99;

    public ?string $language = null;
    public bool $two_factor_enabled = false;

    public function mount(): void
    {
        $this->language            = app(SettingsService::class)->getDefaultLanguage();
        $this->two_factor_enabled  = app(SettingsService::class)->isTwoFactorEnabled();
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('language')
                ->label(__('resources.pages.default_language'))
                ->options(['en' => 'English', 'ar' => 'Arabic'])
                ->required(),
            Toggle::make('two_factor_enabled')
                ->label(__('resources.pages.two_factor_enabled'))
                ->helperText(__('resources.pages.two_factor_helper'))
                ->onColor('success')
                ->offColor('danger'),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $svc  = app(SettingsService::class);
        $svc->set('default_language', $data['language']);
        $svc->set('two_factor_enabled', $data['two_factor_enabled'] ? 'true' : 'false', 'boolean', 'security');
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
