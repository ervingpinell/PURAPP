<?php

namespace App\Helpers;

class EmailHelper
{
    /**
     * Get the company logo for email display.
     * Priorities:
     * 1. Public URL (env COMPANY_LOGO_URL) - Best for Gmail/Webmail.
     * 2. Base64 Data URI - Fallback for local dev or specific clients (Note: Gmail blocks data URIs).
     *
     * @return string URL or Base64 data URI
     */
    public static function getEmbeddedLogo(): string
    {
        // 1. Try public URL from .env (Best for Gmail)
        $publicUrl = env('COMPANY_LOGO_URL');
        if (!empty($publicUrl) && filter_var($publicUrl, FILTER_VALIDATE_URL)) {
            return $publicUrl;
        }

        // 2. Fallback: Local Base64 (Optimized logo)
        $logoPath = public_path('cdn/logos/brand-logo-white-email-optimized.png');

        // Fallback to original if optimized doesn't exist
        if (!file_exists($logoPath)) {
            $logoPath = public_path('cdn/logos/brand-logo-white-email.png');
        }

        // If still not found, return empty string
        if (!file_exists($logoPath)) {
            \Illuminate\Support\Facades\Log::warning('Email logo not found at: ' . $logoPath);
            return '';
        }

        // Read file and convert to base64
        $imageData = base64_encode(file_get_contents($logoPath));
        $mimeType = mime_content_type($logoPath);

        return "data:{$mimeType};base64,{$imageData}";
    }

    /**
     * Get logo dimensions for email display.
     *
     * @return array ['width' => int, 'height' => int]
     */
    public static function getLogoEmailDimensions(): array
    {
        return [
            'width' => 240,
            'height' => 140,
        ];
    }
}
