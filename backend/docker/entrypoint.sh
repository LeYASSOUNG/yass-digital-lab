#!/bin/sh
# ================================================================
# Entrypoint Script — Backend Laravel
# Compatible Render / Railway / Neon / local Docker
# ================================================================
set -e

echo "=============================================="
echo "   Yass Digital Lab — Backend Startup"
echo "=============================================="

# ---------------------------------------------------------------
# Déterminer la DSN de connexion PostgreSQL
# Priorité : DATABASE_URL > variables individuelles DB_*
# ---------------------------------------------------------------
if [ -n "$DATABASE_URL" ]; then
    echo "🔗 Mode DATABASE_URL détecté (Neon/Render/Railway)"
    DB_CONNECT_MODE="url"
else
    echo "🔗 Mode variables individuelles DB_* détecté"
    DB_CONNECT_MODE="vars"
fi

# ---------------------------------------------------------------
# Test de connexion PostgreSQL (max 60s)
# On utilise PHP pour parser DATABASE_URL proprement
# ---------------------------------------------------------------
MAX_RETRIES=30
RETRIES=0

echo "⏳ Attente de PostgreSQL..."

until php -r "
    try {
        \$url = getenv('DATABASE_URL');
        if (\$url) {
            \$p = parse_url(\$url);
            \$host   = \$p['host'];
            \$port   = isset(\$p['port']) ? \$p['port'] : 5432;
            \$dbname = ltrim(\$p['path'], '/');
            \$dbname = explode('?', \$dbname)[0];
            \$user   = \$p['user'];
            \$pass   = \$p['pass'];
            \$dsn    = 'pgsql:host=' . \$host . ';port=' . \$port . ';dbname=' . \$dbname . ';sslmode=require';
            new PDO(\$dsn, \$user, \$pass, [PDO::ATTR_TIMEOUT => 5]);
        } else {
            \$host = getenv('DB_HOST');
            \$port = getenv('DB_PORT') ?: '5432';
            \$db   = getenv('DB_DATABASE');
            \$user = getenv('DB_USERNAME');
            \$pass = getenv('DB_PASSWORD');
            \$dsn  = 'pgsql:host=' . \$host . ';port=' . \$port . ';dbname=' . \$db . ';sslmode=prefer';
            new PDO(\$dsn, \$user, \$pass, [PDO::ATTR_TIMEOUT => 5]);
        }
    } catch (Exception \$e) {
        file_put_contents('/tmp/db_error.log', \$e->getMessage());
        exit(1);
    }
"; do
    RETRIES=$((RETRIES+1))
    if [ $RETRIES -ge $MAX_RETRIES ]; then
        echo "❌ Timeout PostgreSQL ! Dernière erreur :"
        cat /tmp/db_error.log 2>/dev/null || echo "(pas de log)"
        echo "⚠️  Démarrage des services quand même..."
        break
    fi
    echo "   PostgreSQL pas prêt — nouvelle tentative dans 2s... ($RETRIES/$MAX_RETRIES)"
    sleep 2
done

if [ $RETRIES -lt $MAX_RETRIES ]; then
    echo "✅ PostgreSQL est prêt !"
fi

# ---------------------------------------------------------------
# Générer la clé si elle n'existe pas
# ---------------------------------------------------------------
if [ -z "$APP_KEY" ]; then
    echo "🔑 Génération de APP_KEY..."
    php artisan key:generate --force
fi

# ---------------------------------------------------------------
# Migrations (seulement si DB accessible)
# ---------------------------------------------------------------
if [ $RETRIES -lt $MAX_RETRIES ]; then
    echo "🗄️  Exécution des migrations..."
    php artisan migrate --force || echo "⚠️  Migration échouée, on continue quand même..."
else
    echo "⚠️  Migrations ignorées (base de données inaccessible)."
fi

# ---------------------------------------------------------------
# Optimisations production
# ---------------------------------------------------------------
echo "⚡ Optimisation pour la production..."
php artisan config:cache || true
php artisan route:cache  || true
php artisan view:cache   || true

# ---------------------------------------------------------------
# Lien symbolique Storage
# ---------------------------------------------------------------
echo "🔗 Création du lien symbolique storage..."
php artisan storage:link 2>/dev/null || true

echo "✅ Démarrage complet ! Lancement des services..."
echo "=============================================="

exec "$@"
