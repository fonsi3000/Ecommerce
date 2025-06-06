FROM php:8.2-fpm-alpine

# Instala dependencias del sistema y extensiones de PHP
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    nano \
    curl \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    tzdata \
    supervisor \
    shadow \
    nginx \
    openssl \
    netcat-openbsd \
    freetype-dev \
    libjpeg-turbo-dev \
    nodejs \
    npm \
    $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    intl \
    gd \
    && rm -rf /var/cache/apk/*

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia el código fuente completo
COPY . .

# Permisos iniciales para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# Copia configuraciones personalizadas
COPY .deploy/config/php.ini /usr/local/etc/php/php.ini
COPY .deploy/config/supervisor.conf /etc/supervisord.conf
COPY .deploy/config/crontab /etc/crontabs/root
COPY .deploy/entrypoint.sh /entrypoint.sh

# Asigna permisos de ejecución al entrypoint
RUN chmod +x /entrypoint.sh

# Expone el puerto PHP-FPM
EXPOSE 9000

# Define el punto de entrada
CMD ["/entrypoint.sh"]
