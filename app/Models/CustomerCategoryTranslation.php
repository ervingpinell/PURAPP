<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CustomerCategoryTranslation Model
 *
 * Stores translated category names.
 */
class CustomerCategoryTranslation extends Model
{
    protected $table = 'customer_category_translations';

    protected $primaryKey = 'translation_id';

    protected $fillable = [
        'category_id',
        'locale',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'category_id', 'category_id');
    }

    public function categories(Tour $tour)
{
    $locale = app()->getLocale();

    $prices = $tour->prices()
        ->where('is_active', true)
        ->with(['category' => function ($q) use ($locale) {
            $q->select('category_id','slug')
              ->with(['translations' => function ($t) use ($locale) {
                  $t->where('locale', $locale)->select('category_id','locale','name');
              }]);
        }])
        ->orderBy('category_id')
        ->get(['category_id','price','min_quantity','max_quantity','is_active']);

    $data = $prices->map(function ($p) use ($locale) {
        $cat  = $p->category;
        $name = $cat?->translations?->first()?->name ?? $cat?->slug ?? '';
        return [
            'id'         => (int)   $p->category_id,
            'slug'       => (string)($cat->slug ?? ''),
            'name'       => (string)$name,
            'price'      => (float) $p->price,
            'min'        => (int)   $p->min_quantity,
            'max'        => (int)   $p->max_quantity,
            'is_active'  => (bool)  $p->is_active,
            'translation'=> ['locale'=>$locale,'name'=>$name],
        ];
    });

    return response()->json($data);
}

    public function scopeLocale($q, string $locale)
    {
        $locale = str_replace('_', '-', $locale);
        return $q->where('locale', $locale);
    }

}
