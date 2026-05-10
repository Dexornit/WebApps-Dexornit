<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * - If not installed → redirect to /install
     * - If already installed → allow through
     * - If accessing /install and already installed → redirect to /
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $isInstalled = file_exists(storage_path('app/.installed'));

            // If accessing installer routes — let it through regardless
            if ($request->is('install') || $request->is('install/*')) {
                if ($isInstalled) {
                    return redirect('/')->with('info', 'Aplikasi sudah terinstal.');
                }
                return $next($request);
            }

            // If not installed and accessing any other route → redirect to installer
            if (!$isInstalled) {
                return redirect('/install');
            }

            return $next($request);

        } catch (\Exception $e) {
            // Never block on middleware failure (shared hosting safety net)
            // Log it silently and pass through
            try {
                \Illuminate\Support\Facades\Log::error('CheckInstallation middleware error: ' . $e->getMessage());
            } catch (\Exception $logError) {
                // Even logging failed — just continue
            }
            return $next($request);
        }
    }
}
