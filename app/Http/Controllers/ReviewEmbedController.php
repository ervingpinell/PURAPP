<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;

class ReviewEmbedController extends Controller
{
    public function show(Tour $tour, Request $request)
    {
        // Renderiza SOLO el HTML de reviews (sin layout) para usar en <iframe>
        return response()
            ->view('embed.reviews', compact('tour'))
            ->header('X-Robots-Tag', 'noindex, nofollow'); // <- clave
    }
}
