FROM node:22-alpine AS assets

WORKDIR /var/www/html

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run production

FROM php:8.2-fpm-alpine AS php-base

RUN apk add --no-cache \
        freetype \
        icu-libs \
        libjpeg-turbo \
        libpng \
        libxml2 \
        libzip \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        dom \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        xml \
        zip \
    && apk del .build-deps \
    && printf '\nclear_env = no\n' >> /usr/local/etc/php-fpm.d/www.conf \
    && printf '%s\n' \
        'opcache.enable=1' \
        'opcache.validate_timestamps=1' \
        'opcache.memory_consumption=192' \
        'opcache.max_accelerated_files=20000' \
        > /usr/local/etc/php/conf.d/opcache-production.ini

FROM php-base AS app-build

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --optimize-autoloader \
    && mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        bootstrap/cache \
        public/uploads \
        public/upload \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads public/upload \
    && chmod -R ug+rwX storage bootstrap/cache public/uploads public/upload

COPY --from=assets --chown=www-data:www-data /var/www/html/public /var/www/html/public

FROM php-base AS app

WORKDIR /var/www/html

COPY --from=app-build --chown=www-data:www-data /var/www/html /var/www/html

CMD ["php-fpm", "-F"]

FROM nginx:1.28-alpine AS nginx

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app-build /var/www/html/public /var/www/html/public

EXPOSE 80