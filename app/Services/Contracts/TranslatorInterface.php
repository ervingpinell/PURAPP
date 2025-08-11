<?php

namespace App\Services\Contracts;

interface TranslatorInterface
{
    public function detect(string $text): ?string;
    public function translate(string $text, string $targetLocale): string;
    public function translateAll(string $text): array;

    /** Traduce SOLO lo que está dentro de paréntesis; deja intacto lo de afuera. */
    public function translatePreserveOutsideParentheses(string $text, string $targetLocale): string;


}

