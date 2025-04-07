# Etapa 1: Construcción de frontend con Node
FROM node:18 as frontend

WORKDIR /app

# Copia solo lo necesario para el build
COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm install && npm run build

# Etapa 2: PHP + Laravel + Swoole
FROM php:8.2-cli

ENV TZ=America/Bogota

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libbrotli-dev \
    pkg-config \
    libicu-dev \
    supervisor

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensiones PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

RUN docker-php-ext-configure intl

# Instala Swoole
RUN pecl install swoole && \
    docker-php-ext-enable swoole

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define directorio de la app
WORKDIR /var/www/html

# Copia el proyecto Laravel
COPY . .

# Copia assets compilados desde etapa Node
COPY --from=frontend /app/public/build public/build

# Asigna permisos a Laravel
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# Instala dependencias y corre migraciones + seeders
USER www-data
RUN composer install --no-dev --optimize-autoloader && \
    php artisan octane:install --server=swoole && \
    php artisan migrate --seed --force

USER root

EXPOSE 7070

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=7070"]
