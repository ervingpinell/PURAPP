<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Reviews\ReviewAggregator;
use App\Services\Reviews\Drivers\LocalReviewSource;
use App\Services\Reviews\Drivers\HttpJsonReviewSource;
use App\Models\ReviewProvider;

class ReviewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReviewAggregator::class, function ($app) {
            $sources = [];

            // Local (BD)
            $sources['local'] = new LocalReviewSource();

            // Proveedores remotos activos
            $rows = ReviewProvider::where('is_active', true)->orderBy('id')->get();
            foreach ($rows as $prov) {
                $slug = strtolower($prov->slug);
                if ($slug === 'local') {
                    continue; // ya cubierto
                }

                $driver = $prov->driver ?: 'http_json';
                switch ($driver) {
                    case 'http_json':
                    default:
                        $sources[$slug] = new HttpJsonReviewSource($prov);
                        break;
                }
            }

            return new ReviewAggregator($sources);
        });
    }

    public function boot(): void
    {
        //
    }
}
