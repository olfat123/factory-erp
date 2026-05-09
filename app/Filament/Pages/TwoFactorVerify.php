<?php

namespace App\Filament\Pages;

use App\Models\OtpCode;
use App\Notifications\OtpNotification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\RateLimiter;

class TwoFactorVerify extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.two-factor-verify';
    protected static bool $shouldRegisterNavigation = false;

    public string $code = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            $this->redirect(filament()->getLoginUrl());
            return;
        }

        if (session()->get('2fa_verified')) {
            $this->redirect(filament()->getUrl());
            return;
        }

        $this->sendOtp();
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([
            TextInput::make('code')
                ->label(__('auth.otp_code'))
                ->required()
                ->numeric()
                ->length(6)
                ->autofocus(),
        ]);
    }

    public function verify(): void
    {
        $user = auth()->user();
        $key  = 'otp_verify:' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            Notification::make()
                ->title(__('auth.otp_too_many_attempts', ['seconds' => $seconds]))
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $data['code'])
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            RateLimiter::hit($key, 300);
            Notification::make()
                ->title(__('auth.otp_invalid'))
                ->danger()
                ->send();
            return;
        }

        RateLimiter::clear($key);
        $otp->markUsed();
        session()->put('2fa_verified', true);

        $this->redirect(filament()->getUrl());
    }

    public function resend(): void
    {
        $key = 'otp_resend:' . auth()->id();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            Notification::make()
                ->title(__('auth.otp_resend_limit'))
                ->warning()
                ->send();
            return;
        }

        RateLimiter::hit($key, 60);
        $this->sendOtp();

        Notification::make()
            ->title(__('auth.otp_resent'))
            ->success()
            ->send();
    }

    private function sendOtp(): void
    {
        $user = auth()->user();

        // Invalidate previous unused OTPs
        OtpCode::where('user_id', $user->id)->whereNull('used_at')->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new OtpNotification($code));
    }

    public function getTitle(): string
    {
        return __('auth.otp_title');
    }
}
