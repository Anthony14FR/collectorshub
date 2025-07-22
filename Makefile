.PHONY: help install setup start clear-cache refresh log main main-rebuild docker-dev-up docker-dev-restart docker-dev-start docker-prod-up docker-prod-mep docker-prod-seed docker-prod-refresh docker-prod-migrate docker-prod-restart docker-prod-down docker-prod-stop docker-prod-rebuild docker-prod-logs docker-shell docker-clean docker-prod-artisan docker-prod-composer docker-prod-npm

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Installe les dépendances PHP et Node.js
	composer update
	npm install

setup: ## Configure le projet (env, key, dépendances et base de données)
	make install
	@if [ ! -f .env ]; then cp .env.example .env; fi
	php artisan key:generate
	make refresh

start: ## Démarre les serveurs front (Vite) et back (Laravel)
ifeq ($(OS),Windows_NT)
	start "Laravel Server" bash -c "php artisan serve; exec bash" & start "Vite Server" bash -c "npm run dev; exec bash"
else
	php artisan serve --host=0.0.0.0 --port=8000 & npm run dev
endif

clear-cache: ## Nettoie tous les caches
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	php artisan optimize:clear

refresh: ## Rafraîchit la base de données et exécute les seeders
	rm -rf database/database.sqlite
	touch database/database.sqlite
	php artisan migrate:fresh --seed 

log: ## Affiche les 200 dernières lignes des logs Laravel et suit les nouvelles entrées
	tail -f -n 200 storage/logs/laravel.log

main: ## Checkout main et pull
	git checkout main && git pull

main-rebuild: ## Checkout main, pull, et rebuild + refresh
	make main
	make install
	make refresh

# Commandes Docker
docker-dev-up: ## Démarre l'environnement de développement Docker
	docker compose build
	docker compose up -d
	sleep 10
	docker compose exec app php artisan key:generate --force
	docker compose exec app composer install
	docker compose exec app npm install
	docker compose exec app touch /var/www/html/database/database.sqlite
	docker compose exec app php artisan migrate --force
	docker compose exec app php artisan db:seed --force
	docker compose exec app php artisan config:cache

docker-dev-restart: ## Redémarre l'environnement de développement Docker
	docker compose up -d
	sleep 5

docker-dev-start: ## Démarre l'environnement de développement Docker
	docker compose up -d
	sleep 5
	docker compose exec app npm run dev -- --host 0.0.0.0

docker-prod-up: ## Démarre l'environnement de production Docker
	docker compose -f docker-compose.prod.yml build
	docker compose -f docker-compose.prod.yml up -d

docker-prod-mep: ## Rafraîchit l'environnement de production Docker
	make main
	make install
	docker compose -f docker-compose.prod.yml build
	docker compose -f docker-compose.prod.yml up -d
	docker compose -f docker-compose.prod.yml exec app php artisan migrate

docker-prod-seed: ## Exécute les seeders de la base de données de production
	docker compose -f docker-compose.prod.yml exec app php artisan db:seed

docker-prod-refresh: ## Rafraîchit l'environnement de production Docker
	docker compose -f docker-compose.prod.yml exec app php artisan migrate:fresh --seed

docker-prod-migrate: ## Exécute les migrations de la base de données de production
	docker compose -f docker-compose.prod.yml exec app php artisan migrate

docker-prod-restart: ## Redémarre l'environnement de production Docker
	docker compose -f docker-compose.prod.yml down
	docker compose -f docker-compose.prod.yml up -d

docker-prod-stop: ## Arrête les conteneurs Docker
	docker compose -f docker-compose.prod.yml down

docker-prod-rebuild: ## Rebuild l'environnement Docker
	docker compose -f docker-compose.prod.yml down
	docker compose -f docker-compose.prod.yml build
	docker compose -f docker-compose.prod.yml up -d

docker-prod-logs: ## Affiche les logs des conteneurs Docker
	docker compose -f docker-compose.prod.yml logs -f

docker-shell: ## Ouvre un shell dans le conteneur Docker
	docker compose exec app sh

docker-clean: ## Nettoie l'environnement Docker
	@echo "❓ Êtes-vous sûr ? (y/N)"
	@read confirm && [ "$$confirm" = "y" ] || exit 1
	make docker-prod-down
	docker compose -f docker-compose.prod.yml down
	docker system prune -af

docker-prod-artisan: ## Exécute une commande Artisan dans le conteneur Docker
	docker compose -f docker-compose.prod.yml exec app php artisan $(CMD)

docker-prod-composer: ## Exécute une commande Composer dans le conteneur Docker
	docker compose -f docker-compose.prod.yml exec app composer $(CMD)

docker-prod-npm: ## Exécute une commande NPM dans le conteneur Docker
	docker compose -f docker-compose.prod.yml exec app npm $(CMD)