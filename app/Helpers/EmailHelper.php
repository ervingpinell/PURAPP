<?php

namespace App\Helpers;

class EmailHelper
{
    /**
     * Get the company logo as a base64 data URI for email embedding.
     * This ensures the logo always displays in email clients (Gmail, Outlook, etc.)
     * even when external images are blocked.
     *
     * @return string Base64 data URI
     */
    public static function getEmbeddedLogo(): string
    {
        // Path to optimized logo for emails
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
