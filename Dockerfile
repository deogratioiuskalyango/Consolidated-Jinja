FROM node:22-bookworm AS assets

WORKDIR /app/listing
COPY listing/package*.json ./
RUN npm ci
COPY listing ./
RUN npm run prod -- --theme=homzen

FROM composer:2 AS composer-bin

FROM php:8.3-apache-bookworm

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" calendar gd pdo_mysql pdo_pgsql zip \
    && a2enmod rewrite headers \
    && sed -ri "s#/var/www/html#${APACHE_DOCUMENT_ROOT}#g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer-bin /usr/bin/composer /usr/bin/composer
COPY listing ./
COPY --from=assets /app/listing/public/themes/homzen ./public/themes/homzen
COPY --from=assets /app/listing/platform/themes/homzen/public ./platform/themes/homzen/public
COPY docker/render-entrypoint.sh /usr/local/bin/render-entrypoint.sh

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/render-entrypoint.sh

ENTRYPOINT ["render-entrypoint.sh"]
CMD ["apache2-foreground"]
