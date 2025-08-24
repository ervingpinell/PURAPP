<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\Contracts\TranslatorInterface;
use App\Services\DeepLTranslator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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
VerifyEmail::toMailUsing(function ($notifiable, string $url) {
    return (new MailMessage)
        ->subject(__('adminlte::auth.verify.subject'))
        ->greeting(__('adminlte::adminlte.hello') ?? 'Hola')
        ->line(__('adminlte::auth.verify.intro'))
        ->action(__('adminlte::auth.verify.action'), $url)
        ->line(__('adminlte::auth.verify.outro'));
});
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
