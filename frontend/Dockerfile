# ================================================================
# Dockerfile — Frontend Vue 3 (Multi-Stage Build)
# ================================================================
# Stage 1 : builder  → Compile la SPA Vue avec Vite
# Stage 2 : production → Nginx minimaliste pour servir les assets
# ================================================================

# --- Stage 1 : Build Node.js ---
FROM node:22-alpine AS builder

WORKDIR /app

# Copier package.json en premier (cache)
COPY package.json package-lock.json* ./

RUN npm ci --frozen-lockfile

# Copier les sources
COPY . .

# Argument injecté par docker-compose pour pointer l'API backend
ARG VITE_API_BASE_URL=http://localhost:8000/api
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL

# Build de production Vite
RUN npm run build

# ================================================================
# --- Stage 2 : Nginx Production ---
# ================================================================
FROM nginx:1.27-alpine AS production

LABEL maintainer="Yass Digital Lab <diarrass1507@gmail.com>"
LABEL description="Vue 3 SPA Frontend — Yass Digital Lab"

# Supprimer la config Nginx par défaut
RUN rm /etc/nginx/conf.d/default.conf

# Copier notre config Nginx optimisée pour une SPA Vue
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copier le build Vite depuis le stage builder
COPY --from=builder /app/dist /usr/share/nginx/html

# Sécurisation : fichiers en lecture seule pour www-data
RUN chown -R nginx:nginx /usr/share/nginx/html \
    && chmod -R 755 /usr/share/nginx/html

EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

CMD ["nginx", "-g", "daemon off;"]
