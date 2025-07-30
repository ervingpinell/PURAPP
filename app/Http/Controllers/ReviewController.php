<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReviewController extends Controller
{
    public function fetchReviews(Request $request)
    {
        $validated = $request->validate([
            'productCode' => 'required|string',
            'count' => 'nullable|integer|min:1|max:50',
            'start' => 'nullable|integer|min:1', // 🔧 ahora inicia mínimo en 1
            'provider' => 'nullable|in:VIATOR,TRIPADVISOR,ALL',
            'sortBy' => 'nullable|string',
        ]);

        $productCode = $validated['productCode'];
        $count       = $validated['count'] ?? 5;
        $start       = $validated['start'] ?? 1; // ✅ fix aquí
        $provider    = $validated['provider'] ?? 'ALL';
        $sortBy      = $validated['sortBy'] ?? 'MOST_RECENT';

        $body = [
            'productCode' => $productCode,
            'count' => $count,
            'start' => $start,
            'provider' => $provider,
            'sortBy' => $sortBy,
            'reviewsForNonPrimaryLocale' => true,
            'showMachineTranslated' => true,
        ];

        $response = Http::withHeaders([
            'exp-api-key' => env('VIATOR_API_KEY'),
            'Accept' => 'application/json;version=2.0',
            'Accept-Language' => 'en-US',
        ])->post('https://api.sandbox.viator.com/partner/reviews/product', $body);

        if ($response->failed()) {
            return response()->json([
                'error' => 'No se pudieron cargar las reseñas.',
                'status' => $response->status(),
                'details' => $response->json()
            ], 502);
        }

        return response()->json($response->json());
    }
}
