FROM node:20-alpine AS frontend-builder

WORKDIR /app

ENV NODE_ENV=production
ENV VITE_APP_URL=https://collectorshub.fr
ENV VITE_APP_NAME=CollectorsHub

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY vite.config.js tsconfig.json ./
COPY resources ./resources
COPY public ./public

RUN npm install --production=false --ignore-scripts
RUN npm run build

FROM composer:latest AS composer

FROM php:8.2-fpm-alpine AS php-builder

RUN apk add --no-cache \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    autoconf \
    g++ \
    make

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    xml \
    bcmath \
    opcache

RUN pecl install redis && docker-php-ext-enable redis

FROM php:8.2-fpm-alpine AS app

ENV APP_ENV=production
ENV APP_DEBUG=false

RUN apk add --no-cache \
    nginx \
    supervisor \
    zip \
    unzip \
    git \
    curl \
    postgresql-libs

COPY --from=php-builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN adduser -u 1000 -G www-data -s /bin/sh -D www-data 2>/dev/null || true

COPY composer.json composer.lock ./
COPY artisan ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY --from=frontend-builder /app/public/build ./public/build

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN echo "pm.max_children = 10" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.start_servers = 3" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.min_spare_servers = 2" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_spare_servers = 4" >> /usr/local/etc/php-fpm.d/www.conf

RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

FROM app AS development

ENV APP_ENV=local
ENV APP_DEBUG=true

RUN composer install --optimize-autoloader --no-interaction

RUN echo "opcache.enable=0" > /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]

FROM app AS production

EXPOSE 80

CMD ["/usr/local/bin/start.sh"] 