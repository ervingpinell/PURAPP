<?php

namespace App\Http\Controllers;

use App\Models\Faq;

/**
 * FaqController
 *
 * Manages FAQ display.
 */
class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order', 'asc')
            ->orderBy('faq_id', 'desc')
            ->get();
            
        return view('faq.index', compact('faqs'));
    }
}
