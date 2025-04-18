FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=America/Bogota

# 🧱 Dependencias del sistema
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y bash git sudo openssh-client \
    libxml2-dev libonig-dev autoconf gcc g++ make \
    libfreetype6-dev libjpeg-turbo8-dev libpng-dev libzip-dev \
    curl unzip nano software-properties-common ca-certificates

# 🟢 Node.js 18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# 🐘 PHP 8.2 + extensiones necesarias
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl \
    php8.2-xml php8.2-bcmath php8.2-intl php8.2-readline php8.2-pcov php8.2-dev

# 🧩 Instala Swoole para Octane
RUN pecl install swoole && \
    echo "extension=swoole.so" > /etc/php/8.2/mods-available/swoole.ini && \
    phpenmod swoole

# 🎼 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 📁 Copia la app Laravel
WORKDIR /app
COPY . .

# 👷 Prepara Laravel, Vite y Octane
RUN composer install --no-interaction --optimize-autoloader --no-dev && \
    composer require laravel/octane --no-interaction && \
    php artisan octane:install --server=swoole

# 🖼️ Prepara archivos estáticos y optimiza imágenes
RUN npm install && \
    npm run build && \
    # Asegura que las carpetas de imágenes tengan los permisos correctos
    mkdir -p public/images && \
    mkdir -p storage/app/public && \
    # Crea el enlace simbólico para archivos públicos
    php artisan storage:link && \
    # Optimiza caché de rutas y configuración
    php artisan route:cache && \
    php artisan config:cache && \
    php artisan view:cache

# 🔑 Genera clave y establece permisos
RUN php artisan key:generate --force && \
    chown -R www-data:www-data /app && \
    chmod -R 775 storage bootstrap/cache public/images

# 🚀 Comando final para correr Octane en puerto 7070
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=7070", "--workers=4", "--task-workers=2"]

EXPOSE 7070