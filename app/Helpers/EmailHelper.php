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

        // 2. Try branding system logo
        if (function_exists('branding')) {
            $brandingLogo = branding('logo_main', '');
            if (!empty($brandingLogo)) {
                $logoPath = public_path($brandingLogo);
                if (file_exists($logoPath)) {
                    $imageData = base64_encode(file_get_contents($logoPath));
                    $mimeType = mime_content_type($logoPath);
                    return "data:{$mimeType};base64,{$imageData}";
                }
            }
        }

        // 3. Fallback to placeholder
        $logoPath = public_path('images/branding/logo-placeholder.png');

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
