<?php

namespace App\Http\Controllers;

use App\Models\CookiePreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

/**
 * CookieConsentController
 *
 * Manages granular cookie consent preferences.
 */
class CookieConsentController extends Controller
{
    /**
     * Accept all cookies
     */
    public function accept(Request $request): Response
    {
        return $this->savePreferences($request, [
            'essential' => true,
            'functional' => true,
            'analytics' => true,
            'marketing' => true,
        ]);
    }

    /**
     * Reject all non-essential cookies
     */
    public function reject(Request $request): Response
    {
        return $this->savePreferences($request, [
            'essential' => true,
            'functional' => false,
            'analytics' => false,
            'marketing' => false,
        ]);
    }

    /**
     * Save custom cookie preferences
     */
    public function customize(Request $request): Response
    {
        $validated = $request->validate([
            'essential' => 'boolean',
            'functional' => 'boolean',
            'analytics' => 'boolean',
            'marketing' => 'boolean',
        ]);

        // Essential cookies are always required
        $validated['essential'] = true;

        return $this->savePreferences($request, $validated);
    }

    /**
     * Get current cookie preferences
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        $preference = CookiePreference::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->first();

        return response()->json($preference ? $preference->toPreferencesArray() : null);
    }

    /**
     * Save preferences to database, session, and cookie
     */
    protected function savePreferences(Request $request, array $preferences): Response
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        // Save to database
        CookiePreference::updateOrCreate(
            [
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
            ],
            array_merge($preferences, [
                'accepted_at' => now(),
            ])
        );

        // Save to session
        $request->session()->put('cookie_preferences', $preferences);
        $request->session()->put('cookies.accepted_at', now()->toIso8601String());

        // Legacy support
        if ($preferences['analytics'] || $preferences['marketing']) {
            $request->session()->put('cookies.accepted', true);
        } else {
            $request->session()->forget('cookies.accepted');
        }

        // Save to cookie as JSON
        Cookie::queue(cookie(
            name: 'gv_cookie_consent',
            value: json_encode($preferences),
            minutes: 60 * 24 * 365,
            path: '/',
            domain: config('session.domain'),
            secure: (bool) config('session.secure', false),
            httpOnly: true,
            raw: false,
            sameSite: config('session.same_site', 'lax')
        ));

        return response()->noContent();
    }
}
