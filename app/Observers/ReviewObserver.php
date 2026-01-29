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
        if ($review->wasChanged(['rating', 'status', 'is_public', 'title', 'body', 'product_id'])) {
            if ($review->wasChanged('product_id')) {
                $old = $review->getOriginal('product_id');
                if ($old) $this->cache->flushProduct($old);
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

        if ($review->product_id) {
            $this->cache->flushProduct($review->product_id);
        }
    }
}
