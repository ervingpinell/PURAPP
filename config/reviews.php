<?php
return [
    'local' => [
        'slug'  => 'local',
        'name'  => 'Local',
        'driver_class' => \App\Services\Reviews\Drivers\LocalReviewSource::class,
        'locked' => true,
    ],

    'min_public_rating' => (int) env('REVIEWS_MIN_PUBLIC_RATING', 4),


];
