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

        // Viator (mock)
        $viatorSettings = [
            'method' => 'POST',
            'url' => '{config:services.viator.url}',
            'list_path' => 'reviews',
            'headers' => [
                '{config:services.viator.key_header}' => '{config:services.viator.key}',
                'Accept' => 'application/json;version=2.0',
                'Content-Type' => 'application/json'
            ],
            'payload' => [
                'productCode' => '{product_code}',
                'count' => '{limit}',
                'start' => '{start}',
                'provider' => 'Viator',
                'sortBy' => 'MOST_RECENT',
                'reviewsForNonPrimaryLocale' => true,
                'showMachineTranslated' => false
            ],
            'map' => [
                'rating' => 'rating',
                'title' => 'title',
                'body' => 'text',
                'author_name' => [
                    'viatorConsumerName',
                    'consumerName',
                    'userNickname',
                    'userName'
                ],
                'date' => 'publishedDate',
                'provider_review_id' => 'reviewId',
                'product_code' => 'productCode'
            ],
            'extras' => [
                'owner_response' => 'ownerResponse.text',
                'owner_response_date' => 'ownerResponse.date',
                'author_avatar' => 'user.avatarUrl'
            ],
            'filters' => [
                'min_rating' => 4,
                'provider' => [
                    'path' => 'provider',
                    'include' => [
                        'Viator',
                        'VIATOR'
                    ]
                ]
            ],
            'product_map' => [
                '1' => '12732P1',
                '2' => '12732P3',
                '3' => '12732P2',
                '4' => '12732P5',
                '5' => '12732P11',
                '6' => '12732P10',
                '7' => '12732P9'
            ]
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
