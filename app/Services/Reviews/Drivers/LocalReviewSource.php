<?php

namespace App\Services\Reviews\Drivers;

use App\Models\Review;
use App\Services\Reviews\Drivers\Contracts\ReviewSource;

class LocalReviewSource implements ReviewSource
{
    public function fetch(array $opts = []): array
    {
        $q = Review::query()
            ->where('provider', 'local')
            ->where('status', 'published')
            ->where('is_public', true);

        if (!empty($opts['language'])) {
            $q->where('language', $opts['language']);
        }
        if (!empty($opts['tour_id'])) {
            $q->where('tour_id', $opts['tour_id']);
        }

        $limit = (int)($opts['limit'] ?? 12);

        return $q->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function (Review $r) {
                return [
                    'rating'      => (int) $r->rating,
                    'title'       => $r->title,
                    'body'        => $r->body,
                    'author_name' => $r->author_name,
                    'date'        => optional($r->created_at)->toDateString(),
                    'tour_id'     => $r->tour_id,
                ];
            })->all();
    }
}
