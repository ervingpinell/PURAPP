<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * PolicySection Model
 *
 * Represents a section within a policy.
 */
class PolicySection extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'policy_sections';
    protected $primaryKey = 'section_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    public $translatable = ['name', 'content'];

    protected $fillable = [
        'policy_id',
        'name',
        'content',
        'sort_order',
        'is_active',
        'deleted_by',
    ];

    protected $casts = [
        'is_active'  => 'bool',
        'sort_order' => 'int',
        'deleted_at' => 'datetime',
    ];

    /* -------------------- RELACIONES -------------------- */

    public function policy()
    {
        return $this->belongsTo(Policy::class, 'policy_id', 'policy_id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /* -------------------- SCOPES -------------------- */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /* -------------------- ACCESSORS DE COMODIDAD -------------------- */

    public function getDisplayNameAttribute(): string
    {
        return (string) $this->name;
    }

    public function getDisplayContentAttribute(): string
    {
        return (string) $this->content;
    }
}
