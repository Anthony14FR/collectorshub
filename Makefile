.PHONY: help install setup start clear-cache refresh log main main-rebuild docker-up-dev docker-restart docker-dev docker-up-prod docker-down docker-stop docker-rebuild docker-logs docker-shell

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

log: ## Affiche les 200 dernières lignes des logs Laravel et suit les nouvelles entrées
	tail -f -n 200 storage/logs/laravel.log

main: ## Checkout main et pull
	git checkout main && git pull

main-rebuild: ## Checkout main, pull, et rebuild + start
	make main
	make install
	make start

# Commandes Docker

docker-up-dev:
	@echo "🚀 Démarrage de l'environnement de développement Docker..."
	@echo "📦 Construction des images Docker..."
	docker compose build
	@echo "🆙 Démarrage des services..."
	docker compose up -d
	@echo "⏳ Attente que les services soient prêts..."
	sleep 10
	@echo "🔑 Génération de la clé Laravel..."
	docker compose exec app php artisan key:generate --force
	@echo "📦 Installation des dépendances PHP..."
	docker compose exec app composer install
	@echo "📦 Installation des dépendances Node.js..."
	docker compose exec app npm install
	@echo "🗄️ Création de la base de données SQLite..."
	docker compose exec app touch /var/www/html/database/database.sqlite
	@echo "📊 Exécution des migrations..."
	docker compose exec app php artisan migrate --force
	@echo "🌱 Exécution des seeders..."
	docker compose exec app php artisan db:seed --force
	@echo "🎯 Optimisation des caches..."
	docker compose exec app php artisan config:cache
	@echo ""
	@echo "✅ Environnement de développement prêt !"
	@echo "🌐 Application: http://localhost:8000"
	@echo "⚡ Pour démarrer Vite: make docker-vite"
	@echo "📧 Mailpit: http://localhost:8025"
	@echo ""
	@echo "📝 Commandes utiles:"
	@echo "  make docker-restart - Redémarrer (après stop)"
	@echo "  make docker-dev     - Redémarrer + Vite"
	@echo "  make docker-logs    - Voir les logs"
	@echo "  make docker-shell   - Entrer dans le container"
	@echo "  make docker-vite    - Démarrer Vite en mode dev"
	@echo "  make docker-stop    - Arrêter (garde données)"
	@echo "  make docker-down    - Arrêter + reset complet"

docker-restart:
	@echo "🔄 Redémarrage rapide des services Docker..."
	docker compose up -d
	@echo "⏳ Attente que les services soient prêts..."
	sleep 5
	@echo "✅ Services redémarrés !"
	@echo "🌐 Application: http://localhost:8000"
	@echo "📧 Mailpit: http://localhost:8025"
	@echo "💡 Si vous avez fait 'docker-down', utilisez 'make docker-up-dev' à la place."

docker-dev:
	@echo "🔄 Redémarrage complet de l'environnement de développement..."
	docker compose up -d
	@echo "⏳ Attente que les services soient prêts..."
	sleep 5
	@echo "✅ Services redémarrés !"
	@echo "🌐 Application: http://localhost:8000"
	@echo "📧 Mailpit: http://localhost:8025"
	@echo "⚡ Démarrage de Vite..."
	@echo "🌐 Vite sera accessible sur: http://localhost:5173"
	docker compose exec app npm run dev -- --host 0.0.0.0

docker-up-prod:
	@echo "🏭 Démarrage de l'environnement de production..."
	@if [ ! -f .env.production ]; then \
		echo "❌ Fichier .env.production manquant !"; \
		echo "📝 Créez le fichier: cp env.production.example .env.production"; \
		echo "✏️  Puis éditez-le avec vos vraies valeurs"; \
		exit 1; \
	fi
	docker compose -f docker-compose.prod.yml --env-file .env.production build
	docker compose -f docker-compose.prod.yml --env-file .env.production up -d
	@echo "⏳ Attente que les services soient prêts..."
	sleep 15
	docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
	docker compose -f docker-compose.prod.yml exec app php artisan db:seed --force
	@echo "✅ Environnement de production démarré !"

docker-down:
	@echo "🛑 Arrêt et suppression des services Docker..."
	docker compose down -v --remove-orphans
	docker compose -f docker-compose.prod.yml down -v --remove-orphans 2>/dev/null || true
	@echo "🗑️  Suppression de la base de données SQLite..."
	rm -f ./database/database.sqlite
	@echo "✅ Environnement complètement nettoyé. Utilisez 'make docker-up-dev' pour redémarrer."

docker-stop:
	@echo "⏸️  Arrêt des services Docker (sans suppression)..."
	docker compose stop
	@echo "✅ Services arrêtés. Données conservées."
	@echo "🔄 Utilisez 'make docker-restart' pour redémarrer rapidement."

docker-rebuild:
	@echo "🔄 Rebuild de l'environnement Docker..."
	make docker-down
	docker compose build --no-cache
	make docker-up-dev

docker-logs:
	docker compose logs -f

docker-shell:
	docker compose exec app sh

docker-clean:
	@echo "⚠️  Nettoyage complet de Docker..."
	@echo "❓ Êtes-vous sûr ? (y/N)"
	@read confirm && [ "$$confirm" = "y" ] || exit 1
	make docker-down
	docker compose down -v --remove-orphans
	docker system prune -af --volumes

docker-artisan:
	docker compose exec app php artisan $(CMD)

docker-composer:
	docker compose exec app composer $(CMD)

docker-npm:
	docker compose exec app npm $(CMD)

docker-vite:
	@echo "⚡ Démarrage de Vite en mode développement..."
	@echo "🌐 Vite sera accessible sur: http://localhost:5173"
	docker compose exec app npm run dev -- --host 0.0.0.0