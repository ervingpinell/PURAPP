<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Reviews\ReviewAggregator;
use App\Services\Reviews\Drivers\LocalReviewSource;
use App\Services\Reviews\Drivers\ViatorReviewSource;

class ReviewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Instancia única para toda la app
        $this->app->singleton(ReviewAggregator::class, function ($app) {
            return new ReviewAggregator([
                'local'  => $app->make(LocalReviewSource::class),
                'viator' => $app->make(ViatorReviewSource::class),
                // 'google' => $app->make(...),
            ]);
        });

        // (Opcional) patrón con "tags" por si luego agregas más drivers dinámicamente:
        /*
        $this->app->tag(
            [LocalReviewSource::class, ViatorReviewSource::class],
            'review.sources'
        );
        $this->app->singleton(ReviewAggregator::class, function ($app) {
            return new ReviewAggregator($app->tagged('review.sources'));
        });
        */
    }

    public function boot(): void
    {
        //
    }
}
