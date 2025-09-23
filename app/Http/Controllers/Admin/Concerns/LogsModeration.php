<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Review;
use App\Models\ReviewModerationLog;
use Illuminate\Support\Facades\Auth;

trait LogsModeration
{
    protected function logModeration(Review $review, string $action, array $meta = []): void
    {
        ReviewModerationLog::create([
            'review_id'     => $review->id,
            'admin_user_id' => Auth::user()->user_id ?? Auth::id(),
            'action'        => $action,
            'meta'          => $meta,
        ]);
    }
}
