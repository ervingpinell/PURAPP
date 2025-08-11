<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\TranslatorInterface;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('faq_id')->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request, TranslatorInterface $translator)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);

        DB::transaction(function () use ($request, $translator) {
            $question = $request->string('question')->trim();
            $answer   = $request->string('answer')->trim();

            $faq = Faq::create([
                'question'  => $question,
                'answer'    => $answer,
                'is_active' => true,
            ]);

            // Traducción automática a ES, EN, FR, PT, DE (DeepL devuelve el mismo texto si ya está en ese idioma)
            $qTr = $translator->translateAll($question);
            $aTr = $translator->translateAll($answer);

            foreach (['es', 'en', 'fr', 'pt', 'de'] as $lang) {
                FaqTranslation::create([
                    'faq_id'   => $faq->faq_id,
                    'locale'   => $lang,
                    'question' => $qTr[$lang] ?? $question,
                    'answer'   => $aTr[$lang] ?? $answer,
                ]);
            }
        });

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'Pregunta registrada correctamente.');
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);

        $faq->update([
            'question' => $request->string('question')->trim(),
            'answer'   => $request->string('answer')->trim(),
        ]);

        // Nota: no retraducimos aquí; las traducciones se editan en el módulo de traducciones
        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'Pregunta actualizada correctamente.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'Pregunta eliminada.');
    }

    public function toggleStatus(Faq $faq)
    {
        $faq->is_active = ! $faq->is_active;
        $faq->save();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'Estado actualizado.');
    }
}
