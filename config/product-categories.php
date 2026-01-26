<?php

return [
    'categories' => [
        
        'guided_tour' => [
            'url_prefix' => 'tours',
            'singular' => 'Tour',
            'plural' => 'Tours',
            'icon' => 'fas fa-hiking',
            'color' => '#28a745',
            'description' => 'Guided tours with expert local guides',
            'schema_type' => 'TouristTrip',
            'meta_keywords' => ['tours', 'guided tours', 'excursions', 'costa rica tours'],
            
            'subcategories' => [
                'full-day' => [
                    'label' => 'Full Day',
                    'description' => 'Full day tours (6+ hours)',
                    'icon' => 'fas fa-sun',
                    'meta_title' => 'Full Day Tours in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Explore Costa Rica with our full day guided tours. Expert guides, all-inclusive packages.',
                ],
                'half-day' => [
                    'label' => 'Half Day',
                    'description' => 'Half day tours (2-5 hours)',
                    'icon' => 'fas fa-clock',
                    'meta_title' => 'Half Day Tours in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Perfect half day adventures in Costa Rica. Quick escapes with maximum adventure.',
                ],
                'multi-day' => [
                    'label' => 'Multi-Day',
                    'description' => 'Multi-day packages (2+ days)',
                    'icon' => 'fas fa-calendar-alt',
                    'meta_title' => 'Multi-Day Tours in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Extended adventures across Costa Rica. Multi-day tour packages with accommodations.',
                ],
                'night' => [
                    'label' => 'Night Tours',
                    'description' => 'Evening and night tours',
                    'icon' => 'fas fa-moon',
                    'meta_title' => 'Night Tours in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Experience Costa Rica after dark. Wildlife, stargazing, and nocturnal adventures.',
                ],
            ],
        ],
        
        'private_transfer' => [
            'url_prefix' => 'transfers',
            'singular' => 'Transfer',
            'plural' => 'Transfers',
            'icon' => 'fas fa-car',
            'color' => '#007bff',
            'description' => 'Private transportation services',
            'schema_type' => 'TaxiService',
            'meta_keywords' => ['transfers', 'transportation', 'private transfer', 'airport shuttle'],
            
            'subcategories' => [
                'private' => [
                    'label' => 'Private Transfer',
                    'description' => 'Exclusive private transfers',
                    'icon' => 'fas fa-car-side',
                    'meta_title' => 'Private Transfers in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Comfortable private transfers across Costa Rica. Door-to-door service.',
                ],
                'shared' => [
                    'label' => 'Shared Shuttle',
                    'description' => 'Affordable shared shuttles',
                    'icon' => 'fas fa-bus',
                    'meta_title' => 'Shared Shuttles in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Budget-friendly shared shuttle services. Reliable and comfortable.',
                ],
                'luxury' => [
                    'label' => 'Luxury',
                    'description' => 'Premium VIP service',
                    'icon' => 'fas fa-gem',
                    'meta_title' => 'Luxury Transfers in Costa Rica | Green Vacations CR',
                    'meta_description' => 'VIP luxury transportation. Premium vehicles and professional chauffeurs.',
                ],
            ],
        ],
        
        'shuttle_service' => [
            'url_prefix' => 'shuttles',
            'singular' => 'Shuttle',
            'plural' => 'Shuttles',
            'icon' => 'fas fa-bus',
            'color' => '#17a2b8',
            'description' => 'Scheduled shuttle services',
            'schema_type' => 'TaxiService',
            'meta_keywords' => ['shuttles', 'shared transport', 'shuttle bus'],
            
            'subcategories' => [
                'shared' => [
                    'label' => 'Shared Shuttle',
                    'description' => 'Regular shared shuttles',
                    'icon' => 'fas fa-users',
                    'meta_title' => 'Shared Shuttles in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Economical shared shuttle services connecting major destinations.',
                ],
            ],
        ],
        
        'adventure_activity' => [
            'url_prefix' => 'activities',
            'singular' => 'Activity',
            'plural' => 'Activities',
            'icon' => 'fas fa-mountain',
            'color' => '#fd7e14',
            'description' => 'Adventure activities and experiences',
            'schema_type' => 'EventSeries',
            'meta_keywords' => ['activities', 'adventures', 'zipline', 'rafting'],
            
            'subcategories' => [
                'extreme' => [
                    'label' => 'Extreme',
                    'description' => 'High-adrenaline activities',
                    'icon' => 'fas fa-bolt',
                    'meta_title' => 'Extreme Activities in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Adrenaline-pumping extreme adventures. Ziplining, rafting, and more.',
                ],
                'moderate' => [
                    'label' => 'Moderate',
                    'description' => 'Moderate difficulty',
                    'icon' => 'fas fa-hiking',
                    'meta_title' => 'Moderate Activities in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Perfect balance of adventure and accessibility. Suitable for most fitness levels.',
                ],
                'family' => [
                    'label' => 'Family Friendly',
                    'description' => 'Suitable for all ages',
                    'icon' => 'fas fa-heart',
                    'meta_title' => 'Family Activities in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Fun for the whole family. Safe and enjoyable activities for all ages.',
                ],
            ],
        ],
        
        'combo_package' => [
            'url_prefix' => 'packages',
            'singular' => 'Package',
            'plural' => 'Packages',
            'icon' => 'fas fa-box',
            'color' => '#6f42c1',
            'description' => 'Combined tour packages',
            'schema_type' => 'TouristTrip',
            'meta_keywords' => ['packages', 'combos', 'tour packages'],
            
            'subcategories' => [
                'adventure' => [
                    'label' => 'Adventure Packages',
                    'description' => 'Combined adventures',
                    'icon' => 'fas fa-mountain',
                    'meta_title' => 'Adventure Packages in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Multi-activity adventure packages. Best value for thrill-seekers.',
                ],
                'relaxation' => [
                    'label' => 'Relaxation Packages',
                    'description' => 'Spa and wellness',
                    'icon' => 'fas fa-spa',
                    'meta_title' => 'Relaxation Packages in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Unwind with our spa and wellness packages. Perfect for rejuvenation.',
                ],
            ],
        ],
        
        'attraction_pass' => [
            'url_prefix' => 'passes',
            'singular' => 'Pass',
            'plural' => 'Passes',
            'icon' => 'fas fa-ticket-alt',
            'color' => '#e83e8c',
            'description' => 'Entrance passes and tickets',
            'schema_type' => 'Product',
            'meta_keywords' => ['passes', 'tickets', 'entrance', 'admission'],
            
            'subcategories' => [
                'single' => [
                    'label' => 'Single Entry',
                    'description' => 'One-time admission',
                    'icon' => 'fas fa-ticket',
                    'meta_title' => 'Attraction Passes in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Skip the line with our attraction passes. Instant confirmation.',
                ],
                'multi' => [
                    'label' => 'Multi-Pass',
                    'description' => 'Multiple attractions',
                    'icon' => 'fas fa-tickets',
                    'meta_title' => 'Multi-Attraction Passes in Costa Rica | Green Vacations CR',
                    'meta_description' => 'Save with multi-attraction passes. Visit multiple sites at discounted rates.',
                ],
            ],
        ],
    ],
    
    'default' => 'guided_tour',
    
    'aliases' => [
        'products' => 'tours',
        'excursions' => 'tours',
        'transport' => 'transfers',
    ],
];
