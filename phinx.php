<?php
require_once('vendor/autoload.php');

// Environment variables manager
// load our environment files - used to store credentials & configuration
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();


return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/app/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/app/database/seeders'
    ],
    'environments' => [
        // 'default_migration_table' => 'phinxlog',
        // 'default_database' => $_ENV[''],
        'default_environment' => $_ENV['PHINX_ENVIRONMENT'],
        'production' => [
            'migration_table' => 'phinxlog_prod',
            'adapter' => $_ENV['PHINX_DB_DRIVER'],
            'host' => $_ENV['PHINX_DB_HOST'],
            'name' => $_ENV['PHINX_DB_NAME'],
            'user' => $_ENV['PHINX_DB_USER'],
            'pass' => $_ENV['PHINX_DB_PASS'],
            'port' => $_ENV['PHINX_DB_PORT'],
            'charset' => $_ENV['PHINX_DB_CHARSET'],
            'collation' => $_ENV['PHINX_DB_COLLATION'],
        ],
        'development' => [
            'migration_table' => 'phinxlog_dev',
            'adapter' => $_ENV['PHINX_DB_DRIVER'],
            'host' => $_ENV['PHINX_DB_HOST'],
            'name' => $_ENV['PHINX_DB_NAME'],
            'user' => $_ENV['PHINX_DB_USER'],
            'pass' => $_ENV['PHINX_DB_PASS'],
            'port' => $_ENV['PHINX_DB_PORT'],
            'charset' => $_ENV['PHINX_DB_CHARSET'],
            'collation' => $_ENV['PHINX_DB_COLLATION'],
        ],
        'testing' => [
            'migration_table' => 'phinxlog_test',
            'adapter' => $_ENV['PHINX_DB_DRIVER'],
            'host' => $_ENV['PHINX_DB_HOST'],
            'name' => $_ENV['PHINX_DB_NAME'],
            'user' => $_ENV['PHINX_DB_USER'],
            'pass' => $_ENV['PHINX_DB_PASS'],
            'port' => $_ENV['PHINX_DB_PORT'],
            'charset' => $_ENV['PHINX_DB_CHARSET'],
            'collation' => $_ENV['PHINX_DB_COLLATION'],
        ]
    ],
    'version_order' => 'creation'
];