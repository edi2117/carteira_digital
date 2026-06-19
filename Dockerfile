FROM node:22-alpine AS frontend
WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ .
RUN npm run build

FROM php:8.4-fpm-alpine AS base
RUN apk add --no-cache nginx supervisor curl linux-headers postgresql-dev && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql bcmath
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM base AS prod
COPY nginx/prod.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisord.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
COPY src/ /var/www/html
COPY --from=frontend /app/dist /var/www/html/public/dist

WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader && \
    mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database && \
    chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
