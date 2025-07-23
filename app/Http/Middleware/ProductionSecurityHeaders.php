<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProductionSecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (app()->environment('production') && config('production.security.force_https')) {
            // Force HTTPS
            if (!$request->secure() && !app()->runningInConsole()) {
                return redirect()->secure($request->getRequestUri(), 301);
            }

            // Security headers
            $response->headers->set('Strict-Transport-Security', 'max-age=' . config('production.security.hsts_max_age') . '; includeSubDomains; preload');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Content Security Policy
            if (config('production.security.content_security_policy_enabled')) {
                $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net unpkg.com; style-src 'self' 'unsafe-inline' fonts.googleapis.com cdn.jsdelivr.net; font-src 'self' fonts.gstatic.com; img-src 'self' data: blob: https:; connect-src 'self' api.spotify.com accounts.spotify.com; media-src 'self' blob:";
                $response->headers->set('Content-Security-Policy', $csp);
            }
        }

        return $response;
    }
} 