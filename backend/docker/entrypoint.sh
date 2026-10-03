#!/bin/sh
# ================================================================
# Entrypoint Script — Backend Laravel
# S'exécute au démarrage du conteneur avant le CMD
# ================================================================
set -e

echo "=============================================="
echo "   Yass Digital Lab — Backend Startup"
echo "=============================================="

# Attendre que PostgreSQL soit prêt (sécurité supplémentaire)
echo "⏳ Waiting for PostgreSQL..."
until php -r "
    try {
        \$pdo = new PDO(
            'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo 'Connected';
    } catch (Exception \$e) {
        exit(1);
    }
"; do
    echo "   PostgreSQL not ready yet — retrying in 2s..."
    sleep 2
done
echo "✅ PostgreSQL is ready!"

# Générer la clé si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Exécuter les migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Cache de configuration pour performance
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Lien symbolique Storage
echo "🔗 Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

# Seeder si la BDD est vide (optionnel)
# php artisan db:seed --force

echo "✅ Startup complete! Starting services..."
echo "=============================================="

# Passer la main à supervisord (Nginx + PHP-FPM)
exec "$@"
