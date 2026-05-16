<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'name' => getenv('DB_NAME') ?: 'db',
        'user' => getenv('DB_USER') ?: 'db',
        'pass' => getenv('DB_PASS') ?: 'db',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'name' => 'Fahrzeugkosten-Tracker',
        'base_url' => getenv('BASE_URL') ?: 'http://fahrzeugkosten-tracker.ddev.site'
    ]
];
