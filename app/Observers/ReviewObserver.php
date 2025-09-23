<?php

namespace App\Observers;

use App\Models\Review;
use Illuminate\Support\Facades\Cache;

class ReviewObserver
{
    private function bumpKey(string $key): void
    {
        $rev = (int) Cache::get($key, 1);
        Cache::forever($key, $rev + 1);
    }

    private function bump(Review $review): void
    {

        $this->bumpKey('reviews.rev');

        if ($review->tour_id) {
            $this->bumpKey("reviews.rev.tour.{$review->tour_id}");
        }
    }

    public function created(Review $review): void
    {
        $this->bump($review);
    }

    public function updated(Review $review): void
    {
        if ($review->wasChanged([
            'rating', 'status', 'is_public', 'language', 'title', 'body',
            'author_name', 'provider', 'provider_review_id', 'tour_id'
        ])) {
            if ($review->wasChanged('tour_id')) {
                $old = $review->getOriginal('tour_id');
                if ($old) {
                    $this->bumpKey("reviews.rev.tour.{$old}");
                }
            }
            $this->bump($review);
        }
    }

    public function deleted(Review $review): void
    {
        $this->bump($review);
    }

    public function restored(Review $review): void
    {
        $this->bump($review);
    }

    public function forceDeleted(Review $review): void
    {
        $this->bump($review);
    }
}
