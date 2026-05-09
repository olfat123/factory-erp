<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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

    public function mount(): void
    {
        $this->language = app(SettingsService::class)->getDefaultLanguage();
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('language')
                ->label(__('resources.pages.default_language'))
                ->options(['en' => 'English', 'ar' => 'Arabic'])
                ->required(),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        app(SettingsService::class)->set('default_language', $data['language']);
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
