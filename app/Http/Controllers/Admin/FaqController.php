<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\TranslatorInterface;
use Exception;
use App\Services\LoggerHelper;

/**
 * FaqController
 *
 * Manages FAQ display.
 */
class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-faqs'])->only(['index', 'trash']);
        $this->middleware(['can:create-faqs'])->only(['store']);
        $this->middleware(['can:edit-faqs'])->only(['update']);
        $this->middleware(['can:publish-faqs'])->only(['toggleStatus']);
        $this->middleware(['can:delete-faqs'])->only(['destroy']);
        $this->middleware(['can:restore-faqs'])->only(['restore']);
        $this->middleware(['can:force-delete-faqs'])->only(['forceDelete']);
    }

    public function index()
    {
        $faqs = Faq::with('translations')->orderBy('faq_id')->get();
        $trashedCount = Faq::onlyTrashed()->count();
        return view('admin.faqs.index', compact('faqs', 'trashedCount'));
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
                try {
                    $qTr = (array) $translator->translateAll($question);
                } catch (\Throwable $e) {
                    $qTr = [];
                }
                try {
                    $aTr = (array) $translator->translateAll($answer);
                } catch (\Throwable $e) {
                    $aTr = [];
                }

                foreach (['es', 'en', 'fr', 'pt', 'de'] as $lang) {
                    FaqTranslation::create([
                        'faq_id'   => $faq->faq_id,
                        'locale'   => $lang,
                        'question' => $qTr[$lang] ?? $question,
                        'answer'   => $aTr[$lang] ?? $answer,
                    ]);
                }
            });



            LoggerHelper::mutated('FaqController', 'store', 'Faq', null);

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'm_config.faq.created_success');
        } catch (Exception $e) {
            LoggerHelper::exception('FaqController', 'store', 'Faq', null, $e);
            return back()
                ->withInput()
                ->with('error', 'm_config.faq.unexpected_error');
        }
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'translations.*.question' => 'required|string|max:500',
            'translations.*.answer'   => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request, $faq) {
                $translations = $request->input('translations', []);

                // Update or create translations for each locale
                foreach ($translations as $locale => $data) {
                    FaqTranslation::updateOrCreate(
                        [
                            'faq_id' => $faq->faq_id,
                            'locale' => $locale,
                        ],
                        [
                            'question' => $data['question'],
                            'answer' => $data['answer'],
                        ]
                    );
                }

                // Update base FAQ with Spanish translation
                if (isset($translations['es'])) {
                    $faq->update([
                        'question' => $translations['es']['question'],
                        'answer' => $translations['es']['answer'],
                    ]);
                }
            });

            LoggerHelper::mutated('FaqController', 'update', 'Faq', $faq->faq_id);

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'm_config.faq.updated_success');
        } catch (Exception $e) {
            LoggerHelper::exception('FaqController', 'update', 'Faq', $faq->faq_id, $e);
            return back()
                ->withInput()
                ->with('error', 'm_config.faq.unexpected_error');
        }
    }

    public function destroy(Faq $faq)
    {
        try {
            $faq->deleted_by = auth()->id();
            $faq->save();
            $faq->delete();

            LoggerHelper::mutated('FaqController', 'destroy', 'Faq', $faq->faq_id);

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'm_config.faq.deleted_success');
        } catch (Exception $e) {
            LoggerHelper::exception('FaqController', 'destroy', 'Faq', $faq->faq_id, $e);
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

            LoggerHelper::mutated('FaqController', 'toggleStatus', 'Faq', $faq->faq_id, ['is_active' => $faq->is_active]);

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', $key);
        } catch (Exception $e) {
            LoggerHelper::exception('FaqController', 'toggleStatus', 'Faq', $faq->faq_id, $e);
            return back()
                ->with('error', 'm_config.faq.unexpected_error');
        }
    }

    public function trash()
    {
        $faqs = Faq::onlyTrashed()
            ->with('deletedBy')
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.faqs.trash', compact('faqs'));
    }

    public function restore($id)
    {
        $faq = Faq::onlyTrashed()->findOrFail($id);
        $faq->deleted_by = null;
        $faq->save();
        $faq->restore();

        LoggerHelper::mutated('FaqController', 'restore', 'Faq', $id);

        return redirect()
            ->route('admin.faqs.trash')
            ->with('success', 'm_config.faq.restored_success');
    }

    public function forceDelete($id)
    {
        $faq = Faq::onlyTrashed()->findOrFail($id);
        $faq->forceDelete();

        LoggerHelper::mutated('FaqController', 'forceDelete', 'Faq', $id);

        return redirect()
            ->route('admin.faqs.trash')
            ->with('success', 'm_config.faq.force_deleted_success');
    }
}
