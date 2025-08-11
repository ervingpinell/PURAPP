<?php
namespace App\Support;

class TranslateSaver
{
    public static function save(string $modelClass, string $fk, int $id, array $fields, array $translations): void
    {
        foreach (['es','en','fr','pt','de'] as $locale) {
            $payload = [$fk => $id, 'locale' => $locale];
            foreach ($fields as $field => $original) {
                $payload[$field] = $translations[$field][$locale] ?? $original;
            }
            $modelClass::create($payload);
        }
    }
}
