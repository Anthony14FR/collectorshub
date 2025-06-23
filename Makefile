.PHONY: help install setup start clear-cache refresh

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Installe les dépendances PHP et Node.js
	composer install
	npm install

setup: ## Configure le projet (env, key, dépendances et base de données)
	make install
	@if [ ! -f .env ]; then cp .env.example .env; fi
	php artisan key:generate
	make refresh
	@echo "Configuration terminee. Le projet est pret a etre utilise."

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