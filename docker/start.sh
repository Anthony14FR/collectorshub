#!/bin/sh
set -e

echo "🚀 Démarrage de CollectorsHub..."

if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "📦 Création de la base de données SQLite..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

if [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "⏳ Attente de PostgreSQL..."
    until php artisan migrate:status > /dev/null 2>&1; do
        echo "Waiting for database..."
        sleep 2
    done
fi

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate --force
fi

echo "📊 Vérification des migrations..."
php artisan migrate --force > /dev/null 2>&1 || true

if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimisations de production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "✅ Démarrage terminé !"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf 