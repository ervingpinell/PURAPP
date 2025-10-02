<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\Reviews\ReviewsCacheManager;

class ReviewObserver
{
    public function __construct(
        private ReviewsCacheManager $cache
    ) {}

    public function created(Review $review): void
    {
        $this->invalidate($review);
    }

    public function updated(Review $review): void
    {
        if ($review->wasChanged(['rating', 'status', 'is_public', 'title', 'body', 'tour_id'])) {
            if ($review->wasChanged('tour_id')) {
                $old = $review->getOriginal('tour_id');
                if ($old) $this->cache->flushTour($old);
            }
            $this->invalidate($review);
        }
    }

    public function deleted(Review $review): void
    {
        $this->invalidate($review);
    }

    private function invalidate(Review $review): void
    {
        $this->cache->flush(); // Global

        if ($review->tour_id) {
            $this->cache->flushTour($review->tour_id);
        }
    }
}
