<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Legacy XSS filter for older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information sent with requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser APIs (camera, mic, geolocation, payment)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');

        // Content Security Policy — allows Livewire/Alpine.js inline scripts
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data: blob: https://www.gravatar.com https://secure.gravatar.com; " .
            "font-src 'self' data:; " .
            "connect-src 'self' ws: wss:; " .
            "frame-ancestors 'self';"
        );

        // HTTP Strict Transport Security — only in production where HTTPS is used
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Remove headers that leak server information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
