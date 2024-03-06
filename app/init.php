<?php
if (file_exists('../vendor/autoload.php')) {
    require_once('../vendor/autoload.php');
} else {
    die("GalaxyPHP Misconfiguration: Run scripts/composer.sh to setup the dependencies");
}

use Josantonius\Session\Facades\Session as Session;

// Environment variables manager
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

// Session manager
if (!Session::isStarted()) {
    // Start session
    if ($_ENV['APP_USE_CUSTOM_SESSION'] == "true") {
        Session::start([
            'name' => $_ENV['APP_SESSION_NAME'],
            'cookie_domain' => $_ENV['APP_URL'],
            'cookie_path' => $_ENV['APP_STORAGE_DIR'] . 'cookies',
            'save_path' => $_ENV['APP_STORAGE_DIR'] . 'sessions',
        ]);
    } else {
        Session::start([]);
    }
}


require_once('config/database.php');

require_once('core/App.php');

require_once('core/Controller.php');