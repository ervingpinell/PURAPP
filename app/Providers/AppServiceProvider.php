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
use Illuminate\Support\Facades\Cache;

use App\Services\Contracts\TranslatorInterface;
use App\Services\DeepLTranslator;
use App\Services\DraftLimitService;


use App\Observers\ReviewObserver;
use App\Models\Review;
use App\Models\ReviewProvider;
use App\Models\Tour;
use App\Models\TourType;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Traductor (DeepL) disponible por inyección de dependencias en toda la app
        $this->app->singleton(TranslatorInterface::class, function () {
            return new DeepLTranslator();
        });
        $this->app->singleton(DraftLimitService::class, function ($app) {
            return new DraftLimitService();
        });
    }

    public function boot(): void
    {
        // Use Bootstrap pagination for AdminLTE compatibility
        \Illuminate\Pagination\Paginator::useBootstrap();

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
                'verification.public',
                now()->addMinutes((int) config('auth.verification.expire', 60)),
                [
                    'id'   => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

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
                // User->cart() es hasMany, obtenemos el último activo
                $cart = $user->cart()->where('is_active', true)->latest('cart_id')->first();
                $cartItemCount = $cart
                    ? $cart->items()->where('is_active', true)->count()
                    : 0;
            } else {
                // Guest user - check expiration on EVERY page load
                $guestCartCreated = session('guest_cart_created_at');
                $sessionCartItems = session('guest_cart_items', []);

                if (!empty($sessionCartItems) && $guestCartCreated) {
                    $expiryMinutes = (int) \App\Models\Setting::getValue('cart.expiration_minutes', 30);
                    $expiresAt = \Carbon\Carbon::parse($guestCartCreated)->addMinutes($expiryMinutes);

                    // CRITICAL: If expired, clear ALL cart-related session data
                    if (now()->isAfter($expiresAt)) {
                        session()->forget([
                            'guest_cart_items',
                            'guest_cart_created_at',
                            'public_cart_promo',
                            'cart_snapshot',           // Prevent stale payment page access
                            'payment_start_time',      // Reset payment timer
                            'cart_reservation_token',  // Clear any reservation tokens
                        ]);
                        $sessionCartItems = [];
                    } else {
                        $cartItemCount = count($sessionCartItems);
                    }
                }
            }

            $view->with('cartItemCount', $cartItemCount);
        });

        // =========================
        // Tours para el footer (typeMeta + toursByType)
        // =========================
        if (! $this->app->runningInConsole()) {
            View::composer(['partials.footer', 'footer', 'components.footer'], function ($view) {
                $loc = app()->getLocale();
                $fb  = config('app.fallback_locale', 'es');
                $ttl = 60 * 60 * 12; // 12 horas

                // Cache de tipos
                $typeMeta = Cache::remember("footer:typeMeta:{$loc}", $ttl, function () use ($loc, $fb) {
                    return TourType::active()
                        ->with('translations')
                        ->get(['tour_type_id'])
                        ->map(function ($type) use ($loc, $fb) {
                            $tr = optional($type->translations)->firstWhere('locale', $loc)
                                ?? optional($type->translations)->firstWhere('locale', $fb);

                            return [
                                'id'    => $type->tour_type_id,
                                'title' => $tr->name ?? '',
                            ];
                        })
                        ->sortBy('title')
                        ->keyBy('id');
                });

                // Cache de tours agrupados
                $toursByType = Cache::remember("footer:toursByType:{$loc}", $ttl, function () use ($loc, $fb) {
                    $tours = Tour::with(['tourType.translations', 'translations'])
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get([
                            'tour_id',
                            'name',
                            'slug',
                            'tour_type_id',
                        ])
                        ->map(function ($tour) use ($loc, $fb) {
                            $trTour = optional($tour->translations)->firstWhere('locale', $loc)
                                ?? optional($tour->translations)->firstWhere('locale', $fb);

                            $tour->translated_name    = $trTour->name ?? $tour->name;
                            $tour->tour_type_id_group = optional($tour->tourType)->tour_type_id ?? 'uncategorized';

                            return $tour;
                        });

                    return $tours->groupBy(fn($t) => $t->tour_type_id_group);
                });

                $view->with('typeMeta', $typeMeta);
                $view->with('toursByType', $toursByType);
            });
        }

        // =========================
        // Override Config from DB settings
        // =========================
        try {
            // Only if not running in console to avoid migration issues if table doesn't exist
            if (! $this->app->runningInConsole() || ! $this->app->runningUnitTests()) {
                config(['payment.gateways.paypal.enabled' => (bool) setting('payment.gateway.paypal', false)]);
                
                // Override Admin Notification Email from Settings
                $notifyEmail = setting('email.booking_notifications');
                if ($notifyEmail) {
                     config(['mail.notifications.address' => $notifyEmail]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore errors during bootstrap (e.g. DB not ready)
        }
    }

    /**
     * Garantiza que el proveedor 'local' exista y esté bloqueado como de sistema.
     */
    protected function ensureLocalReviewProvider(): void
    {
        if (! Schema::hasTable('review_providers')) return;

        ReviewProvider::withoutEvents(function () {
            $p = ReviewProvider::firstOrNew(['slug' => 'local']);
            $table = $p->getTable();

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

            $dirty = false;
            if (Schema::hasColumn($table, 'driver')     && $p->driver !== 'local') {
                $p->driver = 'local';
                $dirty = true;
            }
            if (Schema::hasColumn($table, 'is_active')  && ! $p->is_active) {
                $p->is_active = true;
                $dirty = true;
            }
            if (Schema::hasColumn($table, 'is_system')  && ! $p->is_system) {
                $p->is_system = true;
                $dirty = true;
            }
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
