<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimitServiceProvider extends ServiceProvider
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
     */
    public function boot(): void
    {
        // API general
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter para operaciones generales de cart
        RateLimiter::for('cart', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Demasiadas solicitudes. Por favor espera un momento.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429, $headers);
                });
        });

        // Rate limiter para formularios sensibles (contact, auth, etc)
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Demasiados intentos. Por favor espera antes de intentar nuevamente.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429, $headers);
                });
        });

        // Rate limiter para autenticación (login, register, password reset)
        RateLimiter::for('auth', function (Request $request) {
            $email = $request->input('email');
            $key = $email ? 'auth:' . $email : 'auth:' . $request->ip();

            return Limit::perMinute(5)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Demasiados intentos de inicio de sesión. Por favor espera antes de intentar nuevamente.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429, $headers);
                });
        });

        // Rate limiter para emails (resend verification, password setup, etc)
        RateLimiter::for('email', function (Request $request) {
            $email = $request->input('email') ?: $request->user()?->email;
            $key = $email ? 'email:' . $email : 'email:' . $request->ip();

            return Limit::perHour(3)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Demasiados correos enviados. Por favor espera antes de solicitar otro.',
                        'retry_after' => $headers['Retry-After'] ?? 3600
                    ], 429, $headers);
                });
        });

        // Rate limiter para pagos
        RateLimiter::for('payment', function (Request $request) {
            return Limit::perMinute(20)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Demasiadas solicitudes de pago. Por favor espera un momento.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429, $headers);
                });
        });

        // Rate limiter para promo codes
        RateLimiter::for('promo', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Demasiados intentos. Por favor espera un momento.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429, $headers);
                });
        });

        // Rate limiter para cookies y preferencias (muy permisivo)
        RateLimiter::for('preferences', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Rate limiter para public readonly actions (muy permisivo)
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        // 🔒 Strict rate limiter for guest checkout (anti-bot)
        RateLimiter::for('guest-checkout', function (Request $request) {
            // If authenticated, use normal limits
            if ($request->user()) {
                return Limit::perMinute(10)->by($request->user()->id);
            }

            // For guests, much stricter with dual limits
            return [
                Limit::perMinute(3)->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->json([
                            'message' => 'Demasiados intentos de checkout. Por favor espera antes de intentar nuevamente.',
                            'retry_after' => $headers['Retry-After'] ?? 60
                        ], 429, $headers);
                    }),
                Limit::perHour(10)->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->json([
                            'message' => 'Has excedido el límite de checkouts por hora. Por favor intenta más tarde.',
                            'retry_after' => $headers['Retry-After'] ?? 3600
                        ], 429, $headers);
                    }),
            ];
        });

        // 🔒 Strict rate limiter for payment initiation (anti-bot)
        RateLimiter::for('payment-initiate', function (Request $request) {
            // Skip rate limiting in debug mode
            if (config('app.debug')) {
                return Limit::none();
            }

            if ($request->user()) {
                return Limit::perMinute(5)->by($request->user()->id);
            }

            return [
                Limit::perMinute(2)->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->json([
                            'message' => 'Demasiados intentos de pago. Por favor espera un momento.',
                            'retry_after' => $headers['Retry-After'] ?? 60
                        ], 429, $headers);
                    }),
                Limit::perHour(5)->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->json([
                            'message' => 'Has excedido el límite de intentos de pago por hora.',
                            'retry_after' => $headers['Retry-After'] ?? 3600
                        ], 429, $headers);
                    }),
            ];
        });
    }
}
