<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ===== Helpers de clave =====
        $byUserOrIp = function (Request $request): string {
            // Usa tu PK personalizada (user_id). Si no hay user, usa IP + UA recortado.
            $uid = optional($request->user())->user_id;
            if ($uid) {
                return 'u:' . $uid;
            }
            // Un poco más específico que solo IP para dev (evita colisiones en proxys)
            $ua  = substr((string) $request->userAgent(), 0, 40);
            return 'ip:' . $request->ip() . '|ua:' . $ua;
        };

        // Si la ruta trae {schedule}, mézclalo en la key (granularidad por horario)
        $withSchedule = function (Request $request, string $baseKey): string {
            $sid = $request->route('schedule');
            return $sid ? $baseKey . '|sid:' . (string) $sid : $baseKey;
        };

        // ===== Valores por ENV (con defaults) =====
        $rpmProductsAdmin   = (int) env('RATE_PRODUCTS_ADMIN_RPM', 120);
        $rpmAdminLight   = (int) env('RATE_ADMIN_LIGHT_RPM', 120);
        $rpmCapAdmin     = (int) env('RATE_CAPACITY_ADMIN_RPM', 120);
        $rpmCapDetails   = (int) env('RATE_CAPACITY_DETAILS_RPM', 240);

        // ===== products-admin =====
        RateLimiter::for('products-admin', function (Request $request) use ($byUserOrIp, $rpmProductsAdmin) {
            $key = $byUserOrIp($request);
            return [Limit::perMinute($rpmProductsAdmin)->by($key)];
        });
        RateLimiter::for(User::class . '::products-admin', function (Request $request, User $user) use ($rpmProductsAdmin) {
            return [Limit::perMinute($rpmProductsAdmin)->by('u:' . $user->user_id)];
        });

        // ===== admin-light =====
        RateLimiter::for('admin-light', function (Request $request) use ($byUserOrIp, $rpmAdminLight) {
            $key = $byUserOrIp($request);
            return [Limit::perMinute($rpmAdminLight)->by($key)];
        });
        RateLimiter::for(User::class . '::admin-light', function (Request $request, User $user) use ($rpmAdminLight) {
            return [Limit::perMinute($rpmAdminLight)->by('u:' . $user->user_id)];
        });

        // ===== capacity-admin (PATCH increase/block) =====
        RateLimiter::for('capacity-admin', function (Request $request) use ($byUserOrIp, $withSchedule, $rpmCapAdmin) {
            $base = $byUserOrIp($request);
            $key  = $withSchedule($request, $base);
            return [Limit::perMinute($rpmCapAdmin)->by($key)];
        });
        RateLimiter::for(User::class . '::capacity-admin', function (Request $request, User $user) use ($withSchedule, $rpmCapAdmin) {
            $base = 'u:' . $user->user_id;
            $key  = $withSchedule($request, $base);
            return [Limit::perMinute($rpmCapAdmin)->by($key)];
        });

        // ===== capacity-details (GET details) =====
        RateLimiter::for('capacity-details', function (Request $request) use ($byUserOrIp, $withSchedule, $rpmCapDetails) {
            $base = $byUserOrIp($request);
            $key  = $withSchedule($request, $base);
            return [Limit::perMinute($rpmCapDetails)->by($key)];
        });
        RateLimiter::for(User::class . '::capacity-details', function (Request $request, User $user) use ($withSchedule, $rpmCapDetails) {
            $base = 'u:' . $user->user_id;
            $key  = $withSchedule($request, $base);
            return [Limit::perMinute($rpmCapDetails)->by($key)];
        });

        // ===== checkout (prevent bot spam) =====
        $rpmCheckout = (int) env('RATE_CHECKOUT_RPM', 10);
        RateLimiter::for('checkout', function (Request $request) use ($byUserOrIp, $rpmCheckout) {
            $key = $byUserOrIp($request);
            return [Limit::perMinute($rpmCheckout)->by($key)];
        });
        RateLimiter::for(User::class . '::checkout', function (Request $request, User $user) use ($rpmCheckout) {
            return [Limit::perMinute($rpmCheckout)->by('u:' . $user->user_id)];
        });

        // ===== Registro de rutas =====
        Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
        Route::middleware('web')->group(base_path('routes/web.php'));
    }
}
