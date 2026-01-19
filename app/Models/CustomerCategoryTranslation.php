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



    public function scopeLocale($q, string $locale)
    {
        $locale = str_replace('_', '-', $locale);
        return $q->where('locale', $locale);
    }

}
