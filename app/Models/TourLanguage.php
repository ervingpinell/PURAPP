<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TourLanguage Model
 *
 * Represents a language offered for a tour.
 *
 * @property int $tour_language_id
 * @property string $name
 * @property bool $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $deletedBy
 */
class TourLanguage extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'tour_language_id';


    protected $table = 'tour_languages';

    protected $fillable = [
        'name',
        'is_active',
        'deleted_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = true;
    public function getRouteKeyName()
    {
        return 'tour_language_id';
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_id');
    }

    /**
     * Scope for finding languages deleted older than days
     */
    public function scopeOlderThan($query, $days)
    {
        return $query->onlyTrashed()->where('deleted_at', '<', now()->subDays($days));
    }
}
