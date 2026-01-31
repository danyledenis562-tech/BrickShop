FROM node:20-bullseye AS node-build
WORKDIR /app
COPY package.json package-lock.json ./
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
RUN npm ci && npm run build

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts

FROM php:8.2-cli-bullseye
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=node-build /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

ENV PORT=10000
CMD ["/usr/local/bin/start.sh"]
