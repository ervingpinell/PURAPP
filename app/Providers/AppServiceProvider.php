<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\Contracts\TranslatorInterface;
use App\Services\DeepLTranslator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TranslatorInterface::class, function () {
            return new DeepLTranslator();
        });
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $cartItemCount = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $cart = $user->cart;
                $cartItemCount = $cart ? $cart->items()->where('is_active', true)->count() : 0;
            }

            $view->with('cartItemCount', $cartItemCount);
        });
    }
}
