<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceTwoFactor
{
    public function __construct(private readonly SettingsService $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        if (! $this->settings->isTwoFactorEnabled()) {
            return $next($request);
        }

        // Already verified in this session
        if ($request->session()->get('2fa_verified')) {
            return $next($request);
        }

        $verifyPath = 'portal/two-factor-verify';

        // Allow the 2FA verify page itself to load
        if ($request->is($verifyPath)) {
            return $next($request);
        }

        // Allow Livewire AJAX calls originating from the verify page
        if ($request->header('X-Livewire') && str_contains((string) $request->header('Referer', ''), 'two-factor-verify')) {
            return $next($request);
        }

        return redirect()->to('/' . $verifyPath);
    }
}
