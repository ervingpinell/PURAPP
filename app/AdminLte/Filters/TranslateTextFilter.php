<?php

namespace App\AdminLte\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class TranslateTextFilter implements FilterInterface
{
    public function transform($item)
    {
        if (app()->bound('translator')) {
            // Only translate if 'trans' is true AND 'text' exists and is a non-empty string
            if (!empty($item['trans']) && isset($item['text']) && is_string($item['text']) && $item['text'] !== '') {
                $item['text'] = __($item['text']);
            }
            // Only translate if 'trans' is true AND 'header' exists and is a non-empty string
            if (!empty($item['trans']) && isset($item['header']) && is_string($item['header']) && $item['header'] !== '') {
                $item['header'] = __($item['header']);
            }
        }
        return $item;
    }
}
