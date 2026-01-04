<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ThrottleCartActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'cart-action:' . $request->ip();

        // 🤖 Rate Limit: Max 10 cart actions per minute
        if (RateLimiter::tooManyAttempts($key, 10)) {
            Log::warning('Cart rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'path' => $request->path()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => __('Too many requests. Please wait a moment.')
                ], 429);
            }

            return back()->with('error', __('Too many requests. Please wait a moment.'));
        }

        RateLimiter::hit($key, 60); // 60 seconds window

        // 🤖 Honeypot check - bots fill hidden fields
        if ($request->filled('website')) {
            Log::warning('Bot detected via honeypot field', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'honeypot_value' => $request->website
            ]);

            // Return generic error to not reveal detection
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => __('Invalid request')
                ], 422);
            }

            abort(422, 'Invalid request');
        }

        return $next($request);
    }
}
