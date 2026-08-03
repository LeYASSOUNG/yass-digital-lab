FROM php:8.3-cli

# Installation des dépendances requises et de l'extension pdo_pgsql pour Neon PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copie des fichiers du projet
COPY . .

# Installation des dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Attribution des permissions aux dossiers de stockage et de cache
RUN chmod -R 777 storage bootstrap/cache

# Port d'écoute dynamique fourni par Render
EXPOSE 8000

# Commande de démarrage
CMD php artisan config:clear && php artisan migrate:fresh --force --seed && php artisan cache:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
