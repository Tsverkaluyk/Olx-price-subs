FROM php:8.2-cli

# Встановлюємо системні залежності та розширення PHP для PostgreSQL
RUN apt-get update && apt-get install -y \
    zip unzip curl libpq-dev git \
    && docker-php-ext-install pdo pdo_pgsql

# Встановлюємо Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
