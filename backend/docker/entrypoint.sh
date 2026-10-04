#!/bin/sh
# ================================================================
# Entrypoint Script — Backend Laravel
# S'exécute au démarrage du conteneur avant le CMD
# ================================================================
set -e

echo "=============================================="
echo "   Yass Digital Lab — Backend Startup"
echo "=============================================="

# ---------------------------------------------------------------
# Si DATABASE_URL est fournie (format Neon/Supabase/Heroku),
# on extrait les variables individuelles dont Laravel a besoin.
# Format attendu : postgresql://user:pass@host/dbname?sslmode=require
# ---------------------------------------------------------------
if [ -n "$DATABASE_URL" ]; then
    echo "🔗 Parsing DATABASE_URL..."
    # Supprimer le préfixe postgresql:// ou postgres://
    _URL=$(echo "$DATABASE_URL" | sed -e 's|^postgres://||' -e 's|^postgresql://||')
    # user:pass@host:port/dbname?params
    _USERINFO=$(echo "$_URL" | cut -d'@' -f1)
    _HOSTINFO=$(echo "$_URL" | cut -d'@' -f2)

    export DB_USERNAME=$(echo "$_USERINFO" | cut -d':' -f1)
    export DB_PASSWORD=$(echo "$_USERINFO" | cut -d':' -f2)

    _HOST_PORT=$(echo "$_HOSTINFO" | cut -d'/' -f1)
    export DB_HOST=$(echo "$_HOST_PORT" | cut -d':' -f1)
    _PORT=$(echo "$_HOST_PORT" | cut -d':' -f2)
    export DB_PORT=${_PORT:-5432}

    _DBNAME=$(echo "$_HOSTINFO" | cut -d'/' -f2 | cut -d'?' -f1)
    export DB_DATABASE="$_DBNAME"

    echo "   Host    : $DB_HOST"
    echo "   Port    : $DB_PORT"
    echo "   Database: $DB_DATABASE"
    echo "   User    : $DB_USERNAME"
fi

# ---------------------------------------------------------------
# Attendre que PostgreSQL soit prêt (max 60s)
# ---------------------------------------------------------------
MAX_RETRIES=30
RETRIES=0

echo "⏳ Waiting for PostgreSQL at $DB_HOST:${DB_PORT:-5432}..."
until php -r "
    try {
        if (getenv('DATABASE_URL')) {
            // Parse DATABASE_URL (format: postgresql://user:pass@host:port/dbname?sslmode=require)
            \$url = getenv('DATABASE_URL');
            \$parts = parse_url(\$url);
            \$host = \$parts['host'];
            \$port = \$parts['port'] ?? 5432;
            \$dbname = ltrim(\$parts['path'], '/');
            \$user = \$parts['user'];
            \$pass = \$parts['pass'];
            \$dsn = \"pgsql:host={\$host};port={\$port};dbname={\$dbname};sslmode=require\";
            \$pdo = new PDO(\$dsn, \$user, \$pass, [PDO::ATTR_TIMEOUT => 5]);
        } else {
            \$dsn = 'pgsql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '5432') . ';dbname=' . getenv('DB_DATABASE') . ';sslmode=prefer';
            \$pdo = new PDO(\$dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_TIMEOUT => 5]);
        }
    } catch (Exception \$e) {
        file_put_contents('/tmp/db_error.log', \$e->getMessage());
        exit(1);
    }
"; do
    RETRIES=$((RETRIES+1))
    if [ $RETRIES -ge $MAX_RETRIES ]; then
        echo "❌ PostgreSQL connection timeout! Last error:"
        cat /tmp/db_error.log 2>/dev/null || echo "(no error log)"
        echo "⚠️  Starting services anyway..."
        break
    fi
    echo "   PostgreSQL not ready yet — retrying in 2s... ($RETRIES/$MAX_RETRIES)"
    sleep 2
done

if [ $RETRIES -lt $MAX_RETRIES ]; then
    echo "✅ PostgreSQL is ready!"
fi

# ---------------------------------------------------------------
# Générer la clé si elle n'existe pas
# ---------------------------------------------------------------
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# ---------------------------------------------------------------
# Migrations (seulement si la DB est accessible)
# ---------------------------------------------------------------
if [ $RETRIES -lt $MAX_RETRIES ]; then
    echo "🗄️  Running database migrations..."
    php artisan migrate --force || echo "⚠️  Migration failed, continuing..."
else
    echo "⚠️  Skipping migrations because database is unreachable."
fi

# ---------------------------------------------------------------
# Optimisations production
# ---------------------------------------------------------------
echo "⚡ Optimizing for production..."
php artisan config:cache  || true
php artisan route:cache   || true
php artisan view:cache    || true

# ---------------------------------------------------------------
# Lien symbolique Storage
# ---------------------------------------------------------------
echo "🔗 Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "✅ Startup complete! Starting services..."
echo "=============================================="

# Passer la main à supervisord (Nginx + PHP-FPM)
exec "$@"
