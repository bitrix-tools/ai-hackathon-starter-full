.PHONY: dev-front dev-php dev-python dev-node prod-php prod-python prod-node status ps down down-all logs logs-nginxproxy clean composer-install composer-update composer-dumpautoload composer db-create db-migrate db-migrate-create db-schema-update db-schema-validate

# Variables
DOCKER_COMPOSE = docker compose

# Development
dev-front:
	@echo "Starting frontend"
	COMPOSE_PROFILES=frontend,cloudpub docker compose --env-file .env up --build

## PHP
dev-php:
	@echo "Starting dev php"
	COMPOSE_PROFILES=frontend,php,cloudpub docker compose --env-file .env up --build

# work with composer
.PHONY: composer-install
composer-install:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli composer install

.PHONY: composer-update
composer-update:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli composer update

.PHONY: composer-dumpautoload
composer-dumpautoload:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli composer dumpautoload

# call composer with any parameters
# make composer install
# make composer "install --no-dev"
# make composer require symfony/http-client
.PHONY: composer
composer:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli composer $(filter-out $@,$(MAKECMDGOALS))

.PHONY: php-cli-sh
php-cli-sh:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli sh

php-cli-app-example:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli bin/console app:example

php-cli-lint-phpstan:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli vendor/bin/phpstan --memory-limit=2G analyse -vvv


# Doctrine/Symfony database commands

# ATTENTION!
# This command drop database and create new database with empty structure with default tables
# You must call this command only for new project!
.PHONY: dev-php-init-database
dev-php-init-database: dev-php-db-drop dev-php-db-create dev-php-db-migrate

.PHONY: dev-php-db-create dev-php-db-drop dev-php-db-migrate dev-php-db-migrate-create dev-php-db-schema-update dev-php-db-schema-validate
dev-php-db-create:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli php bin/console doctrine:database:create --if-not-exists

dev-php-db-drop:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli php bin/console doctrine:database:drop --force --if-exists

dev-php-db-migrate:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli php bin/console doctrine:migrations:migrate --no-interaction

dev-php-db-migrate-create:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli php bin/console make:migration --no-interaction

dev-php-db-migrate-status:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli php bin/console doctrine:migrations:status

dev-php-db-schema-update:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli php bin/console doctrine:schema:update --force

dev-php-db-schema-validate:
	COMPOSE_PROFILES=php-cli $(DOCKER_COMPOSE) run --rm --workdir /var/www php-cli php bin/console doctrine:schema:validate

## Python
dev-python:
	@echo "Starting dev python"
	COMPOSE_PROFILES=frontend,python,cloudpub docker compose --env-file .env up --build

## NodeJs
dev-node:
	@echo "Starting dev node"
	COMPOSE_PROFILES=frontend,node,cloudpub docker compose --env-file .env up --build

# Production
prod-php:
	@echo "Starting prod php environment"
	COMPOSE_PROFILES=php FRONTEND_TARGET=production docker compose up --build -d

prod-python:
	@echo "Starting prod python environment"
	COMPOSE_PROFILES=python FRONTEND_TARGET=production docker compose up --build -d

prod-node:
	@echo "Starting prod node environment"
	COMPOSE_PROFILES=node FRONTEND_TARGET=production docker compose up --build -d

# Utils
status:
	docker stats

ps:
	watch -n 2 docker ps

down:
	docker compose down

down-all:
	docker compose down
	docker compose -f docker-compose.server.yml down

logs:
	docker compose logs -f

logs-nginxproxy:
	docker compose logs -f docker-compose.server.yml

clean:
	docker compose down -v
	docker system prune -f

# Database operations
db-backup:
	docker compose exec database pg_dump -U appuser appdb > backup_$(shell date +%Y%m%d_%H%M%S).sql

db-restore:
	docker compose exec -T database psql -U appuser appdb < $(file)

