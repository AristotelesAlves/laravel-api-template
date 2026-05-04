UID := $(shell id -u)
GID := $(shell id -g)
DOCKER_COMPOSE := docker compose
APP := app
VENDOR ?= app

.PHONY: help rename up down restart build shell logs install setup key migrate seed fresh teste test test-filter xbug coverage phpstan ci-local artisan composer routes tinker queue cache-clear cache-reset optimize-clear fix-permissions perms status

help:
	@echo "Available commands:"
	@echo "  make rename PROJECT=my-api  Rename template metadata for a new project"
	@echo "  make up               Start containers"
	@echo "  make down             Stop and remove containers"
	@echo "  make restart          Restart containers"
	@echo "  make build            Rebuild images and start containers"
	@echo "  make shell            Open a shell in the app container"
	@echo "  make logs             Follow container logs"
	@echo "  make install          Install PHP dependencies"
	@echo "  make setup            Prepare environment, key, migrations, seeders, cache, and permissions"
	@echo "  make key              Generate APP_KEY"
	@echo "  make migrate          Run migrations"
	@echo "  make seed             Run seeders"
	@echo "  make fresh            Recreate database and run seeders"
	@echo "  make teste            Run tests with Artisan"
	@echo "  make test             Run tests"
	@echo "  make test-filter      Run tests by filter: make test-filter FILTER=AuthTest"
	@echo "  make xbug             Run Artisan tests with Xdebug coverage and require at least 80%"
	@echo "  make coverage         Run tests with coverage and require at least 80%"
	@echo "  make phpstan          Run PHPStan static analysis"
	@echo "  make ci-local         Run tests, coverage, and PHPStan"
	@echo "  make artisan CMD=...  Run an Artisan command in the app container"
	@echo "  make composer CMD=... Run a Composer command in the app container"
	@echo "  make routes           List application routes"
	@echo "  make tinker           Open Laravel Tinker"
	@echo "  make queue            Process one queue job"
	@echo "  make cache-clear      Clear Laravel caches"
	@echo "  make cache-reset      Remove stale bootstrap cache and regenerate autoload files"
	@echo "  make fix-permissions  Fix local project permissions"

rename:
	PROJECT="$(PROJECT)" VENDOR="$(VENDOR)" sh scripts/rename-template.sh

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

restart:
	$(DOCKER_COMPOSE) restart

build:
	$(DOCKER_COMPOSE) up -d --build

status:
	$(DOCKER_COMPOSE) ps

shell:
	$(DOCKER_COMPOSE) exec $(APP) sh

logs:
	$(DOCKER_COMPOSE) logs -f

install:
	$(DOCKER_COMPOSE) exec $(APP) composer install

setup: up install key migrate seed cache-reset cache-clear fix-permissions

key:
	$(DOCKER_COMPOSE) exec $(APP) php artisan key:generate

migrate:
	$(DOCKER_COMPOSE) exec $(APP) php artisan migrate

seed:
	$(DOCKER_COMPOSE) exec $(APP) php artisan db:seed

fresh:
	$(DOCKER_COMPOSE) exec $(APP) php artisan migrate:fresh --seed

test:
	$(DOCKER_COMPOSE) exec $(APP) php artisan test

teste: test

test-filter:
	$(DOCKER_COMPOSE) exec $(APP) php artisan test --filter=$(FILTER)

coverage:
	$(DOCKER_COMPOSE) exec -e XDEBUG_MODE=coverage $(APP) php artisan test --coverage --min=80 --coverage-clover=coverage.xml

xbug: coverage

phpstan:
	$(DOCKER_COMPOSE) exec $(APP) ./vendor/bin/phpstan analyse

ci-local: test coverage phpstan

artisan:
	$(DOCKER_COMPOSE) exec $(APP) php artisan $(CMD)

composer:
	$(DOCKER_COMPOSE) exec $(APP) composer $(CMD)

routes:
	$(DOCKER_COMPOSE) exec $(APP) php artisan route:list

tinker:
	$(DOCKER_COMPOSE) exec $(APP) php artisan tinker

queue:
	$(DOCKER_COMPOSE) exec $(APP) php artisan queue:work --once

cache-clear:
	$(DOCKER_COMPOSE) exec $(APP) php artisan optimize:clear

cache-reset:
	$(DOCKER_COMPOSE) exec -u root $(APP) rm -f /var/www/html/bootstrap/cache/config.php /var/www/html/bootstrap/cache/packages.php /var/www/html/bootstrap/cache/services.php
	$(DOCKER_COMPOSE) exec $(APP) composer dump-autoload

optimize-clear: cache-clear

fix-permissions: cache-reset
	$(DOCKER_COMPOSE) exec -u root $(APP) chown -R $(UID):$(GID) /var/www/html/bootstrap/cache /var/www/html/storage /var/www/html/composer.lock
	$(DOCKER_COMPOSE) exec -u root $(APP) chmod -R a+rwX /var/www/html/bootstrap/cache /var/www/html/storage

perms: fix-permissions
