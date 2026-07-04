# =============================================================================
# PetrosLabs — Makefile
# =============================================================================

.DEFAULT_GOAL := help

# Colors
GREEN  := \033[0;32m
YELLOW := \033[0;33m
CYAN   := \033[0;36m
RESET  := \033[0m

## —— PetrosLabs ——————————————————————————————————————————————————————————————

.PHONY: help
help: ## Afficher cette aide
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$|^## )' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "} /^## /{printf "\n$(YELLOW)%s$(RESET)\n", substr($$0,4)} /^[a-zA-Z_-]+/{printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'

## —— Installation ————————————————————————————————————————————————————————————

.PHONY: install
install: ## Installer les dépendances (Composer + AssetMapper)
	composer install

## —— Développement (PHP hôte) ————————————————————————————————————————————————

.PHONY: serve
serve: ## Démarrer le serveur de dev (Symfony CLI, http://localhost:8000)
	symfony serve

.PHONY: tailwind-watch
tailwind-watch: ## Recompiler le CSS Tailwind à la volée
	php bin/console tailwind:build --watch

.PHONY: tailwind-build
tailwind-build: ## Compiler le CSS Tailwind pour la production (minifié)
	php bin/console tailwind:build --minify

.PHONY: cache-clear
cache-clear: ## Vider le cache Symfony
	php bin/console cache:clear

.PHONY: lint
lint: ## Vérifier la syntaxe des templates Twig et des fichiers YAML
	php bin/console lint:twig templates
	php bin/console lint:yaml config translations

.PHONY: test
test: ## Lancer la suite de tests PHPUnit
	php bin/phpunit

## —— Docker (infra symfony_env) ——————————————————————————————————————————————

.PHONY: build
build: ## (Re)construire l'image de l'app (Dockerfile : frankenphp + pdo_pgsql)
	docker compose build

.PHONY: up
up: ## Démarrer le conteneur de l'app
	docker compose up -d

.PHONY: down
down: ## Arrêter le conteneur de l'app
	docker compose down

.PHONY: restart
restart: down up ## Redémarrer le conteneur de l'app

.PHONY: ps
ps: ## Afficher l'état du conteneur
	docker compose ps

.PHONY: logs
logs: ## Afficher les logs du conteneur (mode suivi)
	docker compose logs -f

.PHONY: sh
sh: ## Ouvrir un shell dans le conteneur de l'app
	docker compose exec app sh

.PHONY: console
console: ## Exécuter une commande bin/console dans le conteneur (usage : make console cmd="cache:clear")
	@if [ -z "$(cmd)" ]; then \
		echo "$(YELLOW)Usage: make console cmd=\"cache:clear\"$(RESET)"; \
		exit 1; \
	fi
	docker compose exec app php bin/console $(cmd)

.PHONY: traefik-restart
traefik-restart: ## Relancer Traefik (symfony_env) après un up/build — le docker-proxy partagé n'a pas la permission EVENTS
	cd ../symfony_env && docker compose restart traefik

## —— Base de données (PostgreSQL partagé, symfony_env) ————————————————————————

.PHONY: db-create
db-create: ## Créer la base 'petroslabs' sur le PostgreSQL partagé
	cd ../symfony_env && $(MAKE) db-create name=petroslabs

.PHONY: db-drop
db-drop: ## Supprimer la base 'petroslabs' sur le PostgreSQL partagé
	cd ../symfony_env && $(MAKE) db-drop name=petroslabs
