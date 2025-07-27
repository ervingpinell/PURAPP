<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Compartir cantidad de ítems del carrito con todas las vistas
        View::composer('*', function ($view) {
            $cartItemCount = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $cart = $user->cart; // Asegúrate de tener la relación en el modelo User
                $cartItemCount = $cart ? $cart->items()->where('is_active', true)->count() : 0;
            }

            $view->with('cartItemCount', $cartItemCount);
        });
    }
}
