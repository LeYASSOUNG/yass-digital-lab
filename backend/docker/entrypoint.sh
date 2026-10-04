#!/bin/sh
# ================================================================
# Entrypoint Script — Backend Laravel
# S'exécute au démarrage du conteneur avant le CMD
# ================================================================
set -e

echo "=============================================="
echo "   Yass Digital Lab — Backend Startup"
echo "=============================================="

MAX_RETRIES=30
RETRIES=0

echo "⏳ Waiting for PostgreSQL..."
until php -r "
    try {
        \$pdo = new PDO(
            'pgsql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '5432') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
    } catch (Exception \$e) {
        file_put_contents('/tmp/db_error.log', \$e->getMessage());
        exit(1);
    }
"; do
    RETRIES=\$((RETRIES+1))
    if [ \$RETRIES -ge \$MAX_RETRIES ]; then
        echo "❌ PostgreSQL connection timeout! Last error:"
        cat /tmp/db_error.log || true
        echo "⚠️  Starting services anyway..."
        break
    fi
    echo "   PostgreSQL not ready yet — retrying in 2s..."
    sleep 2
done

if [ \$RETRIES -lt \$MAX_RETRIES ]; then
    echo "✅ PostgreSQL is ready!"
fi

# Générer la clé si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

if [ \$RETRIES -lt \$MAX_RETRIES ]; then
    # Exécuter les migrations uniquement si PostgreSQL est prêt
    echo "🗄️  Running database migrations..."
    php artisan migrate --force || true
else
    echo "⚠️  Skipping migrations because database is unreachable."
fi

# Cache de configuration pour performance
echo "⚡ Optimizing for production..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Lien symbolique Storage
echo "🔗 Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "✅ Startup complete! Starting services..."
echo "=============================================="

# Passer la main à supervisord (Nginx + PHP-FPM)
exec "$@"
