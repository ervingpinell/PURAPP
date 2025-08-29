<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\TranslatorInterface;
use Exception;

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

        try {
            DB::transaction(function () use ($request, $translator) {
                $question = $request->string('question')->trim();
                $answer   = $request->string('answer')->trim();

                $faq = Faq::create([
                    'question'  => $question,
                    'answer'    => $answer,
                    'is_active' => true,
                ]);

                // Traducciones automáticas (si falla, usa original)
                try { $qTr = (array) $translator->translateAll($question); } catch (\Throwable $e) { $qTr = []; }
                try { $aTr = (array) $translator->translateAll($answer);   } catch (\Throwable $e) { $aTr = []; }

                foreach (['es','en','fr','pt','de'] as $lang) {
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
                ->with('success', 'm_config.faq.created_success');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'm_config.faq.unexpected_error');
        }
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);

        try {
            $faq->update([
                'question' => $request->string('question')->trim(),
                'answer'   => $request->string('answer')->trim(),
            ]);

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'm_config.faq.updated_success');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'm_config.faq.unexpected_error');
        }
    }

    public function destroy(Faq $faq)
    {
        try {
            $faq->delete();

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'm_config.faq.deleted_success');
        } catch (Exception $e) {
            return back()
                ->with('error', 'm_config.faq.unexpected_error');
        }
    }

    public function toggleStatus(Faq $faq)
    {
        try {
            $faq->is_active = ! $faq->is_active;
            $faq->save();

            $key = $faq->is_active
                ? 'm_config.faq.activated_success'
                : 'm_config.faq.deactivated_success';

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', $key);
        } catch (Exception $e) {
            return back()
                ->with('error', 'm_config.faq.unexpected_error');
        }
    }
}
