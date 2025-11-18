<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/__init__.php';

use Dotenv\Dotenv;

// CARGAR VARIABLES DESDE .env
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// CREA LA APLICACION
$app = create_app();

// CONFIGURACIÓN DEL SERVIDOR
$host = getenv('APP_HOST') ?: '0.0.0.0';

// 🚀 LA VARIABLE CORRECTA EN RAILWAY ES 'PORT'
$port = getenv('PORT') ?: 8000;

echo "Servidor corriendo en http://$host:$port\n";

// Ejecutar el servidor embebido de PHP
exec("php -S $host:$port -t public");
