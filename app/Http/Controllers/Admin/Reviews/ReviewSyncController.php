<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProviderReviews;
use Illuminate\Http\Request;

/**
 * ReviewSyncController
 *
 * Handles reviewsync operations.
 */
class ReviewSyncController extends Controller
{
    public function sync(Request $request, ?string $provider = null)
    {
        // Límite saneado (1..1000, por si acaso)
        $limit = (int) $request->get('limit', 200);
        $limit = max(1, min(1000, $limit));

        // Encola el job (sin bloquear la UI)
        dispatch(new SyncProviderReviews($provider, $limit));

        // Mensaje i18n
        $target = $provider ?: __('reviews.sync.all');
        return back()->with('ok', __('reviews.sync.queued', ['target' => $target]));
    }
}
