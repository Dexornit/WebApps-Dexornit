<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SocialMedia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share active social media to all views (used in footer & contact)
        View::composer('*', function ($view) {
            if (!isset($view->getData()['socialMedia'])) {
                try {
                    $view->with('socialMedia', SocialMedia::where('is_active', true)->get());
                } catch (\Exception $e) {
                    $view->with('socialMedia', collect([]));
                }
            }
        });
    }
}
