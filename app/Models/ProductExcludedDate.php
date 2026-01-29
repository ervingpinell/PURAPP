<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ProductExcludedDate Model
 *
 * Represents dates when a product is not available.
 */
class ProductExcludedDate extends Model
{
    protected $table = 'product_excluded_dates';
    protected $primaryKey = 'product_excluded_date_id'; // Note: Migration renamed PK of this table? Checking migration... 
    // Migration renamed 'product_excluded_dates' to 'product_excluded_dates', but didn't explicitly rename PK. 
    // Assuming standard 'id' or keeping old name if not changed. 
    // Default assumptions in Laravel are 'id'. 
    // The previous code had `protected $primaryKey = 'product_excluded_date_id';`
    // If migration script didn't rename column `product_excluded_date_id`, we keep it.

    protected $fillable = [
        'product_id',
        'schedule_id', 
        'start_date',
        'end_date',
        'reason',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function schedule()
    {
        return $this->belongsTo(\App\Models\Schedule::class, 'schedule_id', 'schedule_id');
    }
}
