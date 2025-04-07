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
    nodejs \
    npm \
    supervisor

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configura git para el directorio
RUN git config --global --add safe.directory /var/www/html

# Instala extensiones PHP necesarias
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
RUN pecl install swoole && docker-php-ext-enable swoole

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos del proyecto Laravel
COPY . .

# Configura permisos iniciales
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Instala dependencias de Laravel, Octane y prepara todo como www-data
USER www-data
RUN composer install --no-interaction --optimize-autoloader --no-dev && \
    composer require laravel/octane && \
    php artisan octane:install --server=swoole

# Compila los assets frontend con Vite
RUN npm install && npm run build

# Ejecuta migraciones y seeders
RUN php artisan migrate --seed --force

# Regresa a usuario root
USER root

# Permisos finales
RUN chmod -R 755 storage bootstrap/cache

EXPOSE 7070

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=7070"]
