<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MeetingPointTranslation Model
 *
 * Stores translated meeting point details.
 */
class MeetingPointTranslation extends Model
{
    protected $fillable = ['meeting_point_id', 'locale', 'name', 'description', 'instructions'];
    public function meetingPoint(): BelongsTo
    {
        return $this->belongsTo(MeetingPoint::class);
    }
}
