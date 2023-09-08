<?php

return [
    'subscriptions' => [
        'artist-pass_individual' => [
            'price_id' => env("STRIPE_INDIVIDUAL_PRICE"),
            'price' => 2000,
        ],
        'artist-pass_organisation' => [
            'price_id' => env('STRIPE_ORGANISATION_PRICE'),
            'price' => 10000,
        ],
    ],
    'prices' => [
        'solo' => [
            'hourly' => 1700,
            'daily' => 1600,
            'weekly' => 1500,
        ],
        'unfunded' => [
            'hourly' => 2400,
            'daily' => 2200,
            'weekly' => 2000,
        ],
        'funded' => [
            'hourly' => 3300,
            'daily' => 3100,
            'weekly' => 2800,
        ],
        'commercial' => [
            'hourly' => 4200,
            'daily' => 4000,
            'weekly' => 3800,
        ],
        'performance' => [
            'hourly' => 6600,
        ],
    ],
    'tax_rate' => env('BRANDX_TAX_RATE', null),
    'admin_email' => env('BRANDX_EMAIL', null),
    'frontend_url' => env('FRONTEND_URL'),
    'booked_url' => env('BOOKED_URL'),
    'whitelist' => explode(',', env('IP_WHITELIST', '')),
];
