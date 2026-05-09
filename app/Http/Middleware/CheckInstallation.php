<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     * 
     * Mirrors TMail's VerifyInstall pattern:
     * - If not installed → auto key:generate → redirect to /install
     * - If already installed → allow through
     * - If accessing /install and already installed → redirect to /
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $isInstalled = file_exists(storage_path('app/.installed'));

            // If accessing installer routes
            if ($request->is('install') || $request->is('install/*')) {
                if ($isInstalled) {
                    return redirect('/')->with('info', 'Aplikasi sudah terinstal.');
                }
                // Generate APP_KEY if empty (needed for CSRF on installer form)
                if (empty(config('app.key'))) {
                    Artisan::call('key:generate', ['--force' => true]);
                }
                return $next($request);
            }

            // If not installed and accessing any other route
            if (!$isInstalled) {
                // Auto-generate key if missing so installer page works
                if (empty(config('app.key'))) {
                    Artisan::call('key:generate', ['--force' => true]);
                }
                return redirect('/install');
            }

            return $next($request);

        } catch (\Exception $e) {
            // Never block on middleware failure (shared hosting safety net)
            return $next($request);
        }
    }
}
