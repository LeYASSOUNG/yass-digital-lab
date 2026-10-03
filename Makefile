# ================================================================
# Makefile — Yass Digital Lab Docker Commands
# ================================================================
# Usage : make <commande>
# ================================================================

.PHONY: help build up down restart logs shell-backend shell-frontend \
        migrate seed fresh test status clean

# Couleurs
GREEN  = \033[0;32m
YELLOW = \033[0;33m
CYAN   = \033[0;36m
RESET  = \033[0m

help: ## Affiche cette aide
	@echo ""
	@echo "$(CYAN)╔══════════════════════════════════════╗$(RESET)"
	@echo "$(CYAN)║   Yass Digital Lab — Docker CLI       ║$(RESET)"
	@echo "$(CYAN)╚══════════════════════════════════════╝$(RESET)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""

# ── Build & Run ──────────────────────────────────────────────
build: ## Construire toutes les images Docker
	docker compose build --no-cache

up: ## Démarrer tous les services en arrière-plan
	docker compose up -d
	@echo "$(GREEN)✅ Services démarrés !$(RESET)"
	@echo "   Frontend  → http://localhost"
	@echo "   Backend   → http://localhost:8000"
	@echo "   API Docs  → http://localhost:8000/api/health"

dev: ## Démarrer en mode développement (avec logs)
	docker compose up

down: ## Arrêter tous les services
	docker compose down

restart: ## Redémarrer tous les services
	docker compose restart

# ── Logs ─────────────────────────────────────────────────────
logs: ## Voir tous les logs en temps réel
	docker compose logs -f

logs-backend: ## Voir les logs du backend uniquement
	docker compose logs -f backend

logs-frontend: ## Voir les logs du frontend uniquement
	docker compose logs -f frontend

logs-queue: ## Voir les logs du queue worker
	docker compose logs -f queue

# ── Shell ─────────────────────────────────────────────────────
shell-backend: ## Ouvrir un shell dans le conteneur backend
	docker compose exec backend sh

shell-frontend: ## Ouvrir un shell dans le conteneur frontend
	docker compose exec frontend sh

shell-postgres: ## Ouvrir psql dans PostgreSQL
	docker compose exec postgres psql -U $${DB_USERNAME:-postgres} $${DB_DATABASE:-yass_digital_lab}

# ── Laravel ──────────────────────────────────────────────────
migrate: ## Exécuter les migrations Laravel
	docker compose exec backend php artisan migrate

migrate-fresh: ## Réinitialiser et recréer toutes les tables
	docker compose exec backend php artisan migrate:fresh --seed

seed: ## Exécuter les seeders Laravel
	docker compose exec backend php artisan db:seed

tinker: ## Ouvrir Laravel Tinker (REPL)
	docker compose exec backend php artisan tinker

artisan: ## Exécuter une commande artisan (ex: make artisan CMD="route:list")
	docker compose exec backend php artisan $(CMD)

# ── Tests ─────────────────────────────────────────────────────
test: ## Exécuter les tests PHP
	docker compose exec backend php artisan test

# ── Statut ────────────────────────────────────────────────────
status: ## Voir l'état de tous les conteneurs
	docker compose ps

health: ## Vérifier la santé de l'API
	@curl -sf http://localhost:8000/api/health | python3 -m json.tool || echo "API indisponible"

# ── Nettoyage ─────────────────────────────────────────────────
clean: ## Arrêter et supprimer les conteneurs + volumes
	docker compose down -v --remove-orphans
	@echo "$(YELLOW)⚠️  Volumes supprimés (données perdues)$(RESET)"

prune: ## Nettoyer les images Docker inutilisées
	docker system prune -f --volumes
