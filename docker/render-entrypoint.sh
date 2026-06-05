#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

PORT="${PORT:-10000}"
BASE_APP_URL="${APP_URL:-http://localhost}"
BASE_APP_URL="${BASE_APP_URL%/}"
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
mkdir -p source-code/storage/app/public source-code/storage/framework/cache/data source-code/storage/framework/sessions source-code/storage/framework/testing source-code/storage/framework/views source-code/storage/logs source-code/bootstrap/cache
mkdir -p source-code/resources/lang source-code/public/file source-code/public/zaifiles
chown -R www-data:www-data storage bootstrap/cache
chown -R www-data:www-data source-code/storage source-code/bootstrap/cache source-code/resources/lang source-code/public/file source-code/public/zaifiles management

if [ "${MANAGEMENT_MARK_INSTALLED:-true}" = "true" ] && [ ! -f source-code/storage/installed ]; then
    printf '{"d":"%s","i":"%s","u":"%s"}' \
        "$(printf '%s' "${MANAGEMENT_APP_URL:-${BASE_APP_URL}/management}" | base64)" \
        "$(date +%y%m%d%H%M%S)" \
        "$(date +%y%m%d%H%M%S)" > source-code/storage/installed
    chown www-data:www-data source-code/storage/installed
fi

rm -rf management/storage
ln -s ../source-code/storage/app/public management/storage

php artisan storage:link || true
php artisan cms:publish:assets || true
php artisan config:cache
php artisan view:cache

(
    cd source-code
    APP_NAME="${MANAGEMENT_APP_NAME:-JCP Property Management}" \
    APP_ENV="${MANAGEMENT_APP_ENV:-${APP_ENV:-production}}" \
    APP_KEY="${MANAGEMENT_APP_KEY:-${APP_KEY:-}}" \
    APP_DEBUG="${MANAGEMENT_APP_DEBUG:-false}" \
    APP_URL="${MANAGEMENT_APP_URL:-${BASE_APP_URL}/management}" \
    ASSET_URL="${MANAGEMENT_ASSET_URL:-${BASE_APP_URL}/management}" \
    DB_CONNECTION="${MANAGEMENT_DB_CONNECTION:-${DB_CONNECTION:-mysql}}" \
    DB_HOST="${MANAGEMENT_DB_HOST:-${DB_HOST:-127.0.0.1}}" \
    DB_PORT="${MANAGEMENT_DB_PORT:-${DB_PORT:-3306}}" \
    DB_DATABASE="${MANAGEMENT_DB_DATABASE:-management}" \
    DB_USERNAME="${MANAGEMENT_DB_USERNAME:-${DB_USERNAME:-root}}" \
    DB_PASSWORD="${MANAGEMENT_DB_PASSWORD:-${DB_PASSWORD:-}}" \
    MYSQL_ATTR_SSL_CA="${MANAGEMENT_MYSQL_ATTR_SSL_CA:-${MYSQL_ATTR_SSL_CA:-}}" \
    CACHE_DRIVER="${MANAGEMENT_CACHE_DRIVER:-file}" \
    SESSION_DRIVER="${MANAGEMENT_SESSION_DRIVER:-file}" \
    QUEUE_CONNECTION="${MANAGEMENT_QUEUE_CONNECTION:-sync}" \
    FILESYSTEM_DISK="${MANAGEMENT_FILESYSTEM_DISK:-local}" \
    STORAGE_DRIVER="${MANAGEMENT_STORAGE_DRIVER:-public}" \
    php artisan storage:link || true
)

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${MANAGEMENT_RUN_MIGRATIONS:-false}" = "true" ]; then
    (
        cd source-code
        APP_NAME="${MANAGEMENT_APP_NAME:-JCP Property Management}" \
        APP_ENV="${MANAGEMENT_APP_ENV:-${APP_ENV:-production}}" \
        APP_KEY="${MANAGEMENT_APP_KEY:-${APP_KEY:-}}" \
        APP_DEBUG="${MANAGEMENT_APP_DEBUG:-false}" \
        APP_URL="${MANAGEMENT_APP_URL:-${BASE_APP_URL}/management}" \
        ASSET_URL="${MANAGEMENT_ASSET_URL:-${BASE_APP_URL}/management}" \
        DB_CONNECTION="${MANAGEMENT_DB_CONNECTION:-${DB_CONNECTION:-mysql}}" \
        DB_HOST="${MANAGEMENT_DB_HOST:-${DB_HOST:-127.0.0.1}}" \
        DB_PORT="${MANAGEMENT_DB_PORT:-${DB_PORT:-3306}}" \
        DB_DATABASE="${MANAGEMENT_DB_DATABASE:-management}" \
        DB_USERNAME="${MANAGEMENT_DB_USERNAME:-${DB_USERNAME:-root}}" \
        DB_PASSWORD="${MANAGEMENT_DB_PASSWORD:-${DB_PASSWORD:-}}" \
        MYSQL_ATTR_SSL_CA="${MANAGEMENT_MYSQL_ATTR_SSL_CA:-${MYSQL_ATTR_SSL_CA:-}}" \
        CACHE_DRIVER="${MANAGEMENT_CACHE_DRIVER:-file}" \
        SESSION_DRIVER="${MANAGEMENT_SESSION_DRIVER:-file}" \
        QUEUE_CONNECTION="${MANAGEMENT_QUEUE_CONNECTION:-sync}" \
        FILESYSTEM_DISK="${MANAGEMENT_FILESYSTEM_DISK:-local}" \
        STORAGE_DRIVER="${MANAGEMENT_STORAGE_DRIVER:-public}" \
        php artisan migrate --force
    )
fi

exec "$@"
