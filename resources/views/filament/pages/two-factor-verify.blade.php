<x-filament-panels::page>
    <div class="max-w-sm mx-auto">
        <form wire:submit="verify">
            {{ $this->form }}

            <div style="margin-top: 1rem;">
                <x-filament::button type="submit" class="w-full">
                    {{ __('auth.otp_verify_button') }}
                </x-filament::button>
            </div>
        </form>

        <div style="margin-top: 1rem; text-align: center;">
            <button wire:click="resend" type="button"
                style="background: none; border: none; color: #6366f1; cursor: pointer; font-size: 0.875rem; text-decoration: underline;">
                {{ __('auth.otp_resend_link') }}
            </button>
        </div>

        <p style="margin-top: 0.75rem; text-align: center; font-size: 0.8rem; color: #6b7280;">
            {{ __('auth.otp_hint') }}
        </p>
    </div>
</x-filament-panels::page>
