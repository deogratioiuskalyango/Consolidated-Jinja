<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__) . '/source-code';

$managementUrl = rtrim(
    getenv('MANAGEMENT_APP_URL') ?: rtrim(getenv('APP_URL') ?: 'http://localhost', '/') . '/management',
    '/'
);

$environment = [
    'APP_NAME' => getenv('MANAGEMENT_APP_NAME') ?: 'JCP Property Management',
    'APP_ENV' => getenv('MANAGEMENT_APP_ENV') ?: getenv('APP_ENV') ?: 'production',
    'APP_KEY' => getenv('MANAGEMENT_APP_KEY') ?: getenv('APP_KEY') ?: '',
    'APP_DEBUG' => getenv('MANAGEMENT_APP_DEBUG') ?: 'false',
    'APP_URL' => $managementUrl,
    'ASSET_URL' => getenv('MANAGEMENT_ASSET_URL') ?: $managementUrl,
    'DB_CONNECTION' => getenv('MANAGEMENT_DB_CONNECTION') ?: getenv('DB_CONNECTION') ?: 'mysql',
    'DB_HOST' => getenv('MANAGEMENT_DB_HOST') ?: getenv('DB_HOST') ?: '127.0.0.1',
    'DB_PORT' => getenv('MANAGEMENT_DB_PORT') ?: getenv('DB_PORT') ?: '3306',
    'DB_DATABASE' => getenv('MANAGEMENT_DB_DATABASE') ?: 'management',
    'DB_USERNAME' => getenv('MANAGEMENT_DB_USERNAME') ?: getenv('DB_USERNAME') ?: 'root',
    'DB_PASSWORD' => getenv('MANAGEMENT_DB_PASSWORD') ?: getenv('DB_PASSWORD') ?: '',
    'MYSQL_ATTR_SSL_CA' => getenv('MANAGEMENT_MYSQL_ATTR_SSL_CA') ?: getenv('MYSQL_ATTR_SSL_CA') ?: '',
    'CACHE_DRIVER' => getenv('MANAGEMENT_CACHE_DRIVER') ?: 'file',
    'SESSION_DRIVER' => getenv('MANAGEMENT_SESSION_DRIVER') ?: 'file',
    'QUEUE_CONNECTION' => getenv('MANAGEMENT_QUEUE_CONNECTION') ?: 'sync',
    'FILESYSTEM_DISK' => getenv('MANAGEMENT_FILESYSTEM_DISK') ?: 'local',
    'STORAGE_DRIVER' => getenv('MANAGEMENT_STORAGE_DRIVER') ?: 'public',
];

foreach ($environment as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath . '/app/Helper/langHelper.php';
require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
