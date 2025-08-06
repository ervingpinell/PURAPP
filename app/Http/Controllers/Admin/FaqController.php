<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqTranslation;
use Illuminate\Http\Request;
use App\Services\GoogleTranslationService;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::all();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request, GoogleTranslationService $translator)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);

        $faq = Faq::create([
            'question'  => $request->question,
            'answer'    => $request->answer,
            'is_active' => true,
        ]);

        // ✅ Traducir a los 4 idiomas
        foreach (['en', 'pt', 'fr', 'de'] as $lang) {
            FaqTranslation::create([
                'faq_id'  => $faq->faq_id,
                'locale'  => $lang,
                'question'=> $translator->translate($request->question, $lang),
                'answer'  => $translator->translate($request->answer, $lang),
            ]);
        }

        return redirect()->route('admin.faqs.index')->with('success', 'Pregunta registrada correctamente.');
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);

        $faq->update([
            'question' => $request->question,
            'answer'   => $request->answer,
        ]);

        // 🔁 No traducimos aquí, porque la edición de traducciones se hace desde el módulo de traducciones

        return redirect()->route('admin.faqs.index')->with('success', 'Pregunta actualizada correctamente.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'Pregunta eliminada.');
    }

    public function toggleStatus(Faq $faq)
    {
        $faq->is_active = !$faq->is_active;
        $faq->save();

        return redirect()->route('admin.faqs.index')->with('success', 'Estado actualizado.');
    }
}
