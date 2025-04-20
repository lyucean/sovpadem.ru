<?php

// Убираем зависимость от несуществующего файла
// require_once __DIR__ . '/html/env_loader.php';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => getenv('MYSQL_HOST') ?: 'db',
            'name' => getenv('MYSQL_DATABASE') ?: 'sovpadem',
            'user' => getenv('MYSQL_USER') ?: 'sovpadem_user',
            'pass' => getenv('MYSQL_PASSWORD') ?: 'sovpadem_password',
            'port' => '3306',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => getenv('MYSQL_HOST') ?: 'db_dev',
            'name' => getenv('MYSQL_DATABASE') ?: 'sovpadem',
            'user' => getenv('MYSQL_USER') ?: 'sovpadem_user',
            'pass' => getenv('MYSQL_PASSWORD') ?: 'sovpadem_password',
            'port' => '3306',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]
    ],
    'version_order' => 'creation'
];