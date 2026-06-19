FROM node:22-alpine AS frontend
WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ .
RUN npm run build

FROM php:8.4-fpm-alpine AS base
RUN apk add --no-cache nginx supervisor curl && \
    docker-php-ext-install pdo pdo_mysql bcmath
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM base AS prod
COPY nginx/prod.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisord.conf
COPY src/ /var/www/html
COPY --from=frontend /app/dist /var/www/html/public/dist

WORKDIR /var/www/html
RUN cp .env.example .env && \
    composer install --no-dev --optimize-autoloader && \
    php artisan key:generate && \
    touch database/database.sqlite && \
    php artisan storage:link

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database/database.sqlite
RUN php artisan migrate --force

EXPOSE 80
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
