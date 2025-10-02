<?php

namespace App\Services\Reviews\Drivers;

use App\Models\Review;
use App\Models\ReviewProvider;
use App\Services\Reviews\Drivers\Contracts\ReviewSource;

class LocalReviewSource implements ReviewSource
{
    protected int $minStars = 0;

    public function __construct(?ReviewProvider $provider = null)
    {
        // Permite pasar el row; si no, lo buscamos
        $provider = $provider ?: ReviewProvider::where('slug','local')->first();
        $this->minStars = (int) ($provider?->getSetting('min_stars', 0) ?? 0);
    }

    public function fetch(array $opts = []): array
    {
        $q = Review::query()
            ->where('provider', 'local')
            ->where('status', 'published')
            ->where('is_public', true);

        if ($this->minStars > 0) {
            $q->where('rating', '>=', $this->minStars);
        }

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
