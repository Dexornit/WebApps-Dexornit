<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $installedMarker = storage_path('app/.installed');
            $isInstalled = file_exists($installedMarker);
            
            // If accessing installer routes
            if ($request->is('install') || $request->is('install/*')) {
                if ($isInstalled) {
                    // Already installed, redirect to home
                    return redirect('/')->with('error', 'Application is already installed.');
                }
                // Not installed, allow access to installer
                return $next($request);
            }
            
            // If accessing other routes and not installed
            if (!$isInstalled) {
                return redirect('/install');
            }
            
            // Installed and accessing normal routes
            return $next($request);
        } catch (\Exception $e) {
            // If middleware fails, allow request to continue
            // This prevents 500 errors on shared hosting
            return $next($request);
        }
    }
}
