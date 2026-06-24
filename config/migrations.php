<?php

declare(strict_types=1);

return [
    'default' => [
        'table_storage' => [
            'table_name'    => 'doctrine_migration_versions',
            'schema_filter' => '/^(?!password_resets|failed_jobs|jobs|migrations|sessions|cache).*$/',
        ],

        'migrations_paths' => [
            'Database\\Migrations' => database_path('migrations'),
        ],

        'organize_migrations' => 'none',
    ],
];
