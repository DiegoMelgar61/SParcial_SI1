<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/__init__.php';

use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$app = create_app();

$host = getenv("APP_HOST") ?: "0.0.0.0";
$port = getenv("PORT") ?: 8000;

echo "Servidor corriendo en http://$host:$port\n";

exec("php -S $host:$port -t public");
