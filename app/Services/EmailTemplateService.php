<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateContent;

class EmailTemplateService
{
    /**
     * Render email content with variables replaced.
     *
     * @param string $templateKey Template identifier
     * @param string $locale Language code
     * @param array $variables Data for variable replacement
     * @return array ['subject' => string, 'sections' => array] or null if not found
     */
    public function render(string $templateKey, string $locale, array $variables): ?array
    {
        $template = EmailTemplate::findByKey($templateKey);

        if (!$template || !$template->is_active) {
            return null; // Fallback to Blade defaults
        }

        $content = $template->getContentWithFallback($locale);

        if (!$content) {
            return null; // Fallback to Blade defaults
        }

        return [
            'subject' => $content->getSubjectWithVariables($variables),
            'sections' => $content->replaceVariables($variables),
        ];
    }

    /**
     * Check if a template exists and is active.
     */
    public function hasActiveTemplate(string $templateKey): bool
    {
        $template = EmailTemplate::findByKey($templateKey);
        return $template && $template->is_active;
    }

    /**
     * Get all templates grouped by category.
     */
    public function getAllTemplatesGrouped(): array
    {
        $templates = EmailTemplate::with('contents')->get();

        return [
            'customer' => $templates->where('category', 'customer')->values(),
            'admin' => $templates->where('category', 'admin')->values(),
            'other' => $templates->where('category', 'other')->values(),
        ];
    }

    /**
     * Get template with all content translations.
     */
    public function getTemplateWithContents(int $id): ?EmailTemplate
    {
        return EmailTemplate::with('contents')->find($id);
    }

    /**
     * Update or create template content for a specific locale.
     */
    public function updateContent(int $templateId, string $locale, string $subject, array $content): EmailTemplateContent
    {
        return EmailTemplateContent::updateOrCreate(
            [
                'email_template_id' => $templateId,
                'locale' => $locale,
            ],
            [
                'subject' => $subject,
                'content' => $content,
            ]
        );
    }

    /**
     * Toggle template active status.
     */
    public function toggleActive(int $id): bool
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return false;
        }

        $template->is_active = !$template->is_active;
        $template->save();

        return true;
    }

    /**
     * Get available variables for templates.
     */
    public function getAvailableVariables(): array
    {
        return EmailTemplateContent::getAvailableVariables();
    }
}
