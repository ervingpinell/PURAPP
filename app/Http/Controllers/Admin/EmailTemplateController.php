<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    protected EmailTemplateService $templateService;

    public function __construct(EmailTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Display a listing of email templates.
     */
    public function index()
    {
        $templates = $this->templateService->getAllTemplatesGrouped();

        return view('admin.email-templates.index', compact('templates'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(EmailTemplate $template)
    {
        $template->load('contents');
        $variables = $this->templateService->getAvailableVariables();
        $locales = ['es', 'en', 'de', 'fr', 'pt'];

        return view('admin.email-templates.edit', compact('template', 'variables', 'locales'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, EmailTemplate $template)
    {
        $request->validate([
            'locales' => 'required|array',
            'locales.*.locale' => 'required|in:es,en,de,fr,pt',
            'locales.*.subject' => 'required|string|max:255',
            'locales.*.content' => 'required|array',
        ]);

        foreach ($request->input('locales') as $localeData) {
            $this->templateService->updateContent(
                $template->id,
                $localeData['locale'],
                $localeData['subject'],
                $localeData['content']
            );
        }

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Template updated successfully');
    }

    /**
     * Toggle template active status.
     */
    public function toggle(EmailTemplate $template)
    {
        $this->templateService->toggleActive($template->id);

        return response()->json([
            'success' => true,
            'is_active' => $template->fresh()->is_active,
        ]);
    }

    /**
     * Preview template with sample data.
     */
    public function preview(Request $request, EmailTemplate $template)
    {
        $locale = $request->input('locale', 'es');

        // Sample data for preview
        $sampleData = [
            'customer_name' => 'Juan Pérez',
            'customer_email' => 'juan@example.com',
            'booking_reference' => 'GV-' . strtoupper(substr(md5(time()), 0, 8)),
            'booking_id' => '12345',
            'tour_name' => 'Volcán Arenal y Aguas Termales',
            'tour_date' => now()->addDays(7)->format('d/M/Y'),
            'tour_time' => '08:00 AM',
            'tour_language' => 'Español',
            'total_amount' => '$150.00',
            'subtotal' => '$125.00',
            'taxes' => '$25.00',
            'payment_url' => route('payment.show'),
            'booking_url' => '#',
            'company_name' => 'Green Vacations CR',
            'support_email' => config('mail.reply_to.address'),
            'company_phone' => env('COMPANY_PHONE'),
            'app_url' => config('app.url'),
        ];

        $rendered = $this->templateService->render($template->template_key, $locale, $sampleData);

        if (!$rendered) {
            return response()->json(['error' => 'Template not found or inactive'], 404);
        }

        return response()->json($rendered);
    }
}
