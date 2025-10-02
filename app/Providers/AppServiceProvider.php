<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;

use App\Services\Contracts\TranslatorInterface;
use App\Services\DeepLTranslator;

use App\Observers\ReviewObserver;
use App\Models\Review;
use App\Models\ReviewProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Traductor
        $this->app->singleton(TranslatorInterface::class, function () {
            return new DeepLTranslator();
        });
    }

    public function boot(): void
    {
        // =========================
        // URLs: fuerza host/esquema desde APP_URL
        // =========================
        if ($root = config('app.url')) {
            URL::forceRootUrl($root);
            if (str_starts_with($root, 'https://')) {
                URL::forceScheme('https');
            }
        }

        // =========================
        // Si usamos prefijo {locale} en rutas públicas, propagar por defecto
        // =========================
        if (request()->route() && request()->route()->parameter('locale') !== null) {
            URL::defaults(['locale' => App::getLocale()]);
        }

        // =========================
        // Email de verificación: generar SIEMPRE contra la ruta pública firmada
        // =========================
        VerifyEmail::createUrlUsing(function ($notifiable) {
            return URL::temporarySignedRoute(
                'verification.public', // <- definida en routes/web.php
                now()->addMinutes((int) config('auth.verification.expire', 60)),
                [
                    'id'   => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

        // Contenido del email de verificación (usa la URL ya construida arriba)
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            return (new MailMessage)
                ->subject(__('adminlte::auth.verify.subject'))
                ->greeting(__('adminlte::adminlte.hello') ?? 'Hola')
                ->line(__('adminlte::auth.verify.intro'))
                ->action(__('adminlte::auth.verify.action'), $url)
                ->line(__('adminlte::auth.verify.outro'));
        });

        // =========================
        // Observers
        // =========================
        Review::observe(ReviewObserver::class);

        // Asegura que exista el proveedor 'local' y quede bloqueado como de sistema
        $this->ensureLocalReviewProvider();

        // Compatibilidad longitud índices
        Schema::defaultStringLength(191);

        // =========================
        // Cart items para todas las vistas
        // =========================
        View::composer('*', function ($view) {
            $cartItemCount = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $cart = $user->cart;
                $cartItemCount = $cart
                    ? $cart->items()->where('is_active', true)->count()
                    : 0;
            }

            $view->with('cartItemCount', $cartItemCount);
        });
    }

    /**
     * Garantiza que el proveedor 'local' exista y esté bloqueado como de sistema.
     * - Crea si no existe.
     * - Activa is_active.
     * - Marca is_system = true (para bloquear eliminación/edición sensible).
     * - Ajusta driver = 'local' (o el que definas en config).
     */
    protected function ensureLocalReviewProvider(): void
    {
        if (! Schema::hasTable('review_providers')) return;

        ReviewProvider::withoutEvents(function () {
            $p = ReviewProvider::firstOrNew(['slug' => 'local']);

            $table = $p->getTable();

            // Si es nuevo, setea TODO antes del primer save (evita NOT NULL violations)
            if (! $p->exists) {
                if (Schema::hasColumn($table, 'name'))          $p->name = $p->name ?? 'Local';
                if (Schema::hasColumn($table, 'driver'))        $p->driver = 'local';
                if (Schema::hasColumn($table, 'is_active'))     $p->is_active = true;
                if (Schema::hasColumn($table, 'is_system'))     $p->is_system = true;
                if (Schema::hasColumn($table, 'indexable') && $p->indexable === null) $p->indexable = true;
                if (Schema::hasColumn($table, 'cache_ttl_sec') && empty($p->cache_ttl_sec)) $p->cache_ttl_sec = 3600;
                if (Schema::hasColumn($table, 'settings')) {
                    $settings = is_array($p->settings) ? $p->settings : [];
                    $settings['min_stars'] = $settings['min_stars'] ?? 0;
                    $p->settings = $settings;
                }
                $p->save();
                return;
            }

            // Si ya existe, normaliza por si alguien lo tocó
            $dirty = false;
            if (Schema::hasColumn($table, 'driver')     && $p->driver !== 'local') { $p->driver = 'local'; $dirty = true; }
            if (Schema::hasColumn($table, 'is_active')  && ! $p->is_active)        { $p->is_active = true; $dirty = true; }
            if (Schema::hasColumn($table, 'is_system')  && ! $p->is_system)        { $p->is_system = true; $dirty = true; }
            if (Schema::hasColumn($table, 'settings')) {
                $settings = is_array($p->settings) ? $p->settings : [];
                if (! array_key_exists('min_stars', $settings)) {
                    $settings['min_stars'] = 0;
                    $p->settings = $settings;
                    $dirty = true;
                }
            }
            if ($dirty) $p->save();
        });
    }
}
