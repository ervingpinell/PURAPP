<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use App\Models\ReviewProvider;

class ReviewProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Local
        ReviewProvider::updateOrCreate(
            ['slug' => 'local'],
            [
                'name'          => 'Local',
                'driver'        => \App\Services\Reviews\Drivers\LocalReviewSource::class,
                'indexable'     => true,
                'settings'      => [], // sin secretos
                'cache_ttl_sec' => 3600,
                'is_active'     => true,
            ]
        );

        // Viator (mock) – NO indexable
        $viatorSettings = [
            // si tienes VIATOR_API_KEY, lo ciframos en secrets.api_key
            'api_key' => env('VIATOR_API_KEY'), // será cifrado por el mutator del modelo
            // otros settings no sensibles:
            'product_map' => [],
        ];

        ReviewProvider::updateOrCreate(
            ['slug' => 'viator'],
            [
                'name'          => 'Viator',
                'driver'        => \App\Services\Reviews\Drivers\ViatorReviewSource::class,
                'indexable'     => false,
                'settings'      => $viatorSettings,
                'cache_ttl_sec' => 3600,
                'is_active'     => true,
            ]
        );
    }
}
