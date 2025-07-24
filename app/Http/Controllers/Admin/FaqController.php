<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::all();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        Faq::create($request->only('question', 'answer') + ['is_active' => true]);

        return redirect()->route('admin.faqs.index')->with('success', 'Pregunta registrada correctamente.');
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq->update($request->only('question', 'answer'));

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
