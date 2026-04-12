FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libssl-dev \
    zip unzip git curl

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

RUN mkdir -p /var/www/html/storage/app/public \
    && mkdir -p /var/www/html/public/storage \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

EXPOSE 8000
CMD ["sh", "-c", "php artisan migrate --force && php artisan storage:link --force && php artisan queue:work --tries=3 --sleep=3 --daemon & php artisan serve --host=0.0.0.0 --port=8000"]