<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AdminLteBrandingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     * 
     * Override AdminLTE config with branding values after app boots
     */
    public function boot(): void
    {
        // Use a view composer to inject branding values into AdminLTE views
        // This works even when config is cached
        view()->composer('adminlte::*', function ($view) {
            // Get branding values
            $logoAdmin = branding('logo_adminlte');
            $companyName = branding('company_name');

            // Override config values dynamically
            if ($logoAdmin) {
                Config::set('adminlte.logo_img', $logoAdmin);
                Config::set('adminlte.auth_logo.img.path', $logoAdmin);
                Config::set('adminlte.preloader.img.path', $logoAdmin);
            }

            if ($companyName) {
                Config::set('adminlte.logo', $companyName);
                Config::set('adminlte.logo_img_alt', $companyName);
            }
        });
    }
}
