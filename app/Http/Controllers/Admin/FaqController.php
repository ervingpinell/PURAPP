<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Faq;
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

    public function index(Request $request)
    {
        // Default always use custom order sort_order
        // The admin can "apply" specialized sorts which rewrite the sort_order column
        // The admin can "apply" specialized sorts which rewrite the sort_order column
        $faqs = Faq::query()
            ->orderBy('sort_order', 'asc')
            ->orderBy('faq_id', 'desc')
            ->get();
            
        $trashedCount = Faq::onlyTrashed()->count();

        return view('admin.faqs.index', compact('faqs', 'trashedCount'));
    }

    // ... existing ...

    public function reorderBulk(Request $request)
    {
        $type = $request->input('type');
        $direction = $request->input('direction', 'asc'); // 'asc' or 'desc'
        
        \Illuminate\Support\Facades\Log::info('ReorderBulk started', ['type' => $type, 'direction' => $direction]);

        if (!in_array($type, ['id', 'alpha'])) {
             return back()->with('error', 'Tipo de ordenamiento inválido.');
        }

        try {
            DB::transaction(function () use ($type, $direction) {
                // Fetch all active/inactive (non-trashed) faqs
                $query = Faq::query();
                
                if ($type === 'id') {
                    $query->orderBy('faq_id', $direction); 
                } 

                $items = $query->get();

                if ($type === 'alpha') {
                    // Sort by question (current locale or fallback)
                    $locale = app()->getLocale();
                    $callback = function($faq) use ($locale) {
                        // Ensure string for sorting
                        return strtolower((string)($faq->translate($locale)?->question ?? $faq->question ?? ''));
                    };

                    if ($direction === 'asc') {
                        $items = $items->sortBy($callback, SORT_NATURAL|SORT_FLAG_CASE);
                    } else {
                        $items = $items->sortByDesc($callback, SORT_NATURAL|SORT_FLAG_CASE);
                    }
                }

                $count = 1;
                foreach ($items as $faq) {
                    $faq->sort_order = $count++;
                    $faq->save();
                }
            });

            try {
                LoggerHelper::mutated('FaqController', 'reorderBulk', 'Faq', null, ['type' => $type, 'direction' => $direction]);
            } catch (\Throwable $loggingError) {
                \Illuminate\Support\Facades\Log::error('LoggerHelper failed in reorderBulk', ['error' => $loggingError->getMessage()]);
            }

            return back()->with('success', 'Orden actualizado correctamente.');
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('ReorderBulk failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            try {
                LoggerHelper::exception('FaqController', 'reorderBulk', 'Faq', null, $e);
            } catch (\Throwable $loggingError) {
                // Ignore nested logging error
            }
            
            return back()->with('error', 'Error al reordenar: ' . $e->getMessage());
        }
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

                $maxOrder = Faq::max('sort_order') ?? 0;

                $faq = Faq::create([
                    'question'  => $question,
                    'answer'    => $answer,
                    'is_active' => true,
                    'sort_order' => $maxOrder + 1,
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

                $faq->setTranslation('question', 'es', $question);
                $faq->setTranslation('answer', 'es', $answer);

                foreach (['en', 'fr', 'pt', 'de'] as $lang) {
                     $faq->setTranslation('question', $lang, $qTr[$lang] ?? $question);
                     $faq->setTranslation('answer', $lang, $aTr[$lang] ?? $answer);
                }
                $faq->save();
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
                    $faq->setTranslation('question', $locale, $data['question']);
                    $faq->setTranslation('answer', $locale, $data['answer']);
                }
                $faq->save();
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

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:faqs,faq_id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->order as $index => $id) {
                    Faq::where('faq_id', $id)->update(['sort_order' => $index + 1]);
                }
            });

            LoggerHelper::mutated('FaqController', 'reorder', 'Faq', null, ['count' => count($request->order)]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            LoggerHelper::exception('FaqController', 'reorder', 'Faq', null, $e);
            return response()->json(['success' => false], 500);
        }
    }
}
