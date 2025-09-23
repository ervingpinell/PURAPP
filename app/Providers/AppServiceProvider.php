<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Services\Contracts\TranslatorInterface;
use App\Services\DeepLTranslator;
use App\Observers\ReviewObserver;
use App\Models\Review;
use App\Models\ReviewProvider;

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
        // Observers
        Review::observe(ReviewObserver::class);

        // Asegura que exista el proveedor 'local' y quede bloqueado como de sistema
        $this->ensureLocalReviewProvider();

        // Email de verificación personalizado
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            return (new MailMessage)
                ->subject(__('adminlte::auth.verify.subject'))
                ->greeting(__('adminlte::adminlte.hello') ?? 'Hola')
                ->line(__('adminlte::auth.verify.intro'))
                ->action(__('adminlte::auth.verify.action'), $url)
                ->line(__('adminlte::auth.verify.outro'));
        });

        // Compatibilidad longitud índices
        Schema::defaultStringLength(191);

        // Cart items para todas las vistas
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
        if (! Schema::hasTable('review_providers')) {
            return;
        }

        $slug   = config('reviews.local.slug', 'local');
        $name   = config('reviews.local.name', 'Local');
        // Puedes usar una clase si así manejas drivers: config('reviews.local.driver_class')
        // Aquí dejamos un string sencillo:
        $driver = 'local';

        ReviewProvider::withoutEvents(function () use ($slug, $name, $driver) {
            // Crea si no existe (con mínimos seguros)
            $provider = ReviewProvider::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );

            $dirty = false;
            $table = $provider->getTable();

            // Sincroniza campos relevantes SOLO si existen en la tabla
            if (Schema::hasColumn($table, 'name') && $provider->name !== $name) {
                $provider->name = $name;
                $dirty = true;
            }
            if (Schema::hasColumn($table, 'driver') && $provider->driver !== $driver) {
                $provider->driver = $driver;
                $dirty = true;
            }
            if (Schema::hasColumn($table, 'is_active') && ! $provider->is_active) {
                $provider->is_active = true;
                $dirty = true;
            }
            if (Schema::hasColumn($table, 'is_system') && ! $provider->is_system) {
                $provider->is_system = true;
                $dirty = true;
            }
            if (Schema::hasColumn($table, 'settings') && empty($provider->settings)) {
                // Mantén arreglo vacío (evita JSON null si está casteado)
                $provider->settings = [];
                $dirty = true;
            }

            if ($dirty) {
                $provider->save();
            }
        });
    }
}
