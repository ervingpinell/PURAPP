<?php

namespace App\AdminLte\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class TranslateTextFilter implements FilterInterface
{
    public function transform($item)
    {
        if (app()->bound('translator')) {
            if (!empty($item['trans']) && !empty($item['text']) && is_string($item['text'])) {
                $item['text'] = __($item['text']);
            }
            if (!empty($item['trans']) && !empty($item['header']) && is_string($item['header'])) {
                $item['header'] = __($item['header']);
            }
        }
        return $item;
    }
}
