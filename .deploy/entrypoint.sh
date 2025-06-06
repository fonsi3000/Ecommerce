#!/bin/sh

echo "⏳ Esperando que MySQL esté disponible..."
until nc -z expo_db 3306; do
  echo "MySQL aún no responde, reintentando..."
  sleep 2
done
echo "✅ MySQL disponible"

echo "⏳ Esperando que Redis esté disponible..."
until nc -z expo_redis 6379; do
  echo "Redis aún no responde, reintentando..."
  sleep 2
done
echo "✅ Redis disponible"

# Git safe directory para evitar advertencias
git config --global --add safe.directory /var/www/html

echo "🛠 Asignando permisos iniciales a carpetas necesarias..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

echo "📦 Instalando dependencias con Composer..."
composer install --no-dev --optimize-autoloader || {
  echo "❌ Falló composer install"
  exit 1
}

echo "🎨 Instalando y compilando assets frontend (npm)..."
npm install
npm run build || {
  echo "❌ Falló compilación de frontend"
  exit 1
}

echo "🔐 Reasignando permisos después del build..."
chown -R www-data:www-data /var/www/html/public /var/www/html/storage
chmod -R 755 /var/www/html/public /var/www/html/storage

echo "⚙️ Ejecutando comandos de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan key:generate --force

echo "🔗 Creando symlink de storage (si no existe)..."
php artisan storage:link || true

echo "✅ Laravel listo para producción"

# Inicia Supervisor (PHP-FPM + cron)
exec supervisord -c /etc/supervisord.conf
