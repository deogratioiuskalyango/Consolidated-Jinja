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
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath calendar curl gd pdo_mysql pdo_pgsql zip \
    && a2enmod rewrite headers \
    && sed -ri "s#/var/www/html#${APACHE_DOCUMENT_ROOT}#g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf \
    && printf '%s\n' \
        'Alias /management /var/www/html/management' \
        '<Directory /var/www/html/management>' \
        '    Options FollowSymLinks' \
        '    AllowOverride All' \
        '    Require all granted' \
        '</Directory>' \
        > /etc/apache2/conf-available/management.conf \
    && a2enconf management \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer-bin /usr/bin/composer /usr/bin/composer
COPY listing ./
COPY source-code ./source-code
COPY management ./management
COPY --from=assets /app/listing/public/themes/homzen ./public/themes/homzen
COPY --from=assets /app/listing/platform/themes/homzen/public ./platform/themes/homzen/public
COPY docker/render-entrypoint.sh /usr/local/bin/render-entrypoint.sh

RUN mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && rm -f bootstrap/cache/*.php \
    && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p source-code/storage/app/public source-code/storage/framework/cache/data source-code/storage/framework/sessions source-code/storage/framework/testing source-code/storage/framework/views source-code/storage/logs source-code/bootstrap/cache \
    && rm -f source-code/bootstrap/cache/*.php \
    && (cd source-code && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader) \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chown -R www-data:www-data source-code/storage source-code/bootstrap/cache management \
    && chmod +x /usr/local/bin/render-entrypoint.sh

ENTRYPOINT ["render-entrypoint.sh"]
CMD ["apache2-foreground"]
