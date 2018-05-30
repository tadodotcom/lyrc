<?php

return [
    // Consider this in the future, for now we require the routes file inside this defined group
    // 'namespace' => 'Modules\InstallersClub\Http\Controllers',
    // 'prefix' => 'installersclub',
    'source_file_path' => 'routes.yml',
    'compiled_file_path' => '',

    'countries' => [
        'at' => ['de', 'en'],
        'au' => ['en'],
        'be' => ['fr', 'en', 'nl'],
        'bg' => ['en'],
        'ca' => ['en', 'fr'],
        'ch' => ['de', 'en', 'fr', 'it'],
        'cy' => ['en'],
        'cz' => ['en'],
        'de' => ['de', 'en'],
        'dk' => ['en'],
        'ee' => ['en'],
        'en' => ['en'],
        'es' => ['es'],
        'fi' => ['en'],
        'fr' => ['fr'],
        'gb' => ['en'],
        'gr' => ['en'],
        'hr' => ['en'],
        'hu' => ['en'],
        'ie' => ['en'],
        'it' => ['it'],
        'lt' => ['en'],
        'lu' => ['en'],
        'lv' => ['en'],
        'mt' => ['en'],
        'nl' => ['nl'],
        'no' => ['en'],
        'pl' => ['en'],
        'pt' => ['en'],
        'ro' => ['en'],
        'se' => ['en'],
        'sg' => ['en'],
        'si' => ['en'],
        'sk' => ['en'],
        'us' => ['en', 'es']
    ],

    'defaults' => [
        'expirationType' => 'dynamic',
        'https' => true,
        'sitemap' => true
    ],

    'locales' => [
        'DSC' => ['nl'],
        'ESC' => ['en', 'gb', 'us'],
        'FSC' => ['fr'],
        'GSC' => ['de', 'at', 'ch'],
        'ISC' => ['it', 'ch'],
        'SSC' => ['es', 'us']
    ],
];
