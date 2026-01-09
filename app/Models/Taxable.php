<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Taxable Model
 *
 * Polymorphic relationship for taxable entities.
 */
class Taxable extends Pivot
{
    protected $table = 'taxables';

    public $incrementing = true;

    /**
     * Get the tax
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id', 'tax_id');
    }

    /**
     * Get the owning taxable model (Tour, Product, etc.)
     */
    public function taxable()
    {
        return $this->morphTo();
    }
}
