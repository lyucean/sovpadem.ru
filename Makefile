.PHONY: dev prod down clean clean-all rebuild rebuild-dev rebuild-prod migrate migrate-dev migrate-prod create-migration rollback seed composer-install composer-install-dev composer-install-prod wait-for-db wait-for-db-dev wait-for-db-prod help

help:
	@echo "Available commands:"
	@echo "  make dev                - Start development environment"
	@echo "  make prod               - Start production environment"
	@echo "  make down               - Stop all containers"
	@echo "  make clean              - Stop and remove all containers, volumes"
	@echo "  make rebuild            - Clean all and restart the last used environment"
	@echo "  make rebuild-dev        - Clean all and restart development environment"
	@echo "  make rebuild-prod       - Clean all and restart production environment"
	@echo "  make migrate            - Run migrations in the current environment"
	@echo "  make migrate-dev        - Run migrations in development environment"
	@echo "  make migrate-prod       - Run migrations in production environment"
	@echo "  make create-migration   - Create a new migration (usage: make create-migration name=MigrationName)"
	@echo "  make rollback           - Rollback the last migration"
	@echo "  make seed               - Run database seeders"
	@echo "  make composer-install   - Run composer install in the current environment"
	@echo "  make composer-install-dev - Run composer install in development environment"
	@echo "  make composer-install-prod - Run composer install in production environment"
	@echo "  make wait-for-db        - Wait for database to be available in the current environment"
	@echo "  make wait-for-db-dev    - Wait for development database to be available"
	@echo "  make wait-for-db-prod   - Wait for production database to be available"
	@echo "  make help               - Show this help message"

dev:
	docker compose --profile dev up -d
	@echo "Installing composer dependencies..."
	@make composer-install-dev
	@echo "Waiting for database to be available..."
	@make wait-for-db-dev
	@echo "Running migrations..."
	@make migrate-dev
	@echo "Development environment started at http://localhost:8080"

prod:
	docker compose --profile prod up -d
	@echo "Installing composer dependencies..."
	@make composer-install-prod
	@echo "Waiting for database to be available..."
	@make wait-for-db-prod
	@echo "Running migrations..."
	@make migrate-prod
	@echo "Production environment started at https://sovpadem.ru"

down:
	docker compose down

clean:
	docker compose down -v
	@echo "All containers and volumes removed"

rebuild: clean
	@if [ -f .env ] && grep -q "APP_ENV=prod" .env; then \
		make prod; \
	else \
		make dev; \
	fi
	@echo "Environment rebuilt and restarted"

rebuild-dev: clean
	make dev
	@echo "Development environment rebuilt and restarted"

rebuild-prod: clean
	make prod
	@echo "Production environment rebuilt and restarted"

# Определяем, какой контейнер использовать в зависимости от окружения
define get_container
$(shell if [ -f .env ] && grep -q "APP_ENV=prod" .env; then \
	echo "sovpadem_web"; \
else \
	echo "sovpadem_web_dev"; \
fi)
endef

# Установка MySQL клиента в контейнере
install-mysql-client:
	@container=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "sovpadem_web"; else echo "sovpadem_web_dev"; fi); \
	echo "Installing MySQL client in $$container..."; \
	docker exec $$container bash -c "if ! command -v mysql &> /dev/null; then apt-get update && apt-get install -y default-mysql-client; fi"

install-mysql-client-dev:
	@echo "Installing MySQL client in development environment..."
	@docker exec sovpadem_web_dev bash -c "if ! command -v mysql &> /dev/null; then apt-get update && apt-get install -y default-mysql-client; fi"

install-mysql-client-prod:
	@echo "Installing MySQL client in production environment..."
	@docker exec sovpadem_web bash -c "if ! command -v mysql &> /dev/null; then apt-get update && apt-get install -y default-mysql-client; fi"

# Ожидание доступности базы данных
wait-for-db: install-mysql-client
	@container=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "sovpadem_web"; else echo "sovpadem_web_dev"; fi); \
	db_host=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "sovpadem_db"; else echo "db_dev"; fi); \
	echo "Waiting for database at $$db_host to be available..."; \
	docker exec $$container bash -c "cd /var/www && /var/www/html/scripts/wait-for-db.sh $$db_host"

wait-for-db-dev: install-mysql-client-dev
	@echo "Waiting for development database to be available..."
	@docker exec sovpadem_web_dev bash -c "cd /var/www && /var/www/html/scripts/wait-for-db.sh db_dev"

wait-for-db-prod: install-mysql-client-prod
	@echo "Waiting for production database to be available..."
	@docker exec sovpadem_web bash -c "cd /var/www && /var/www/html/scripts/wait-for-db.sh sovpadem_db"

# Composer install
composer-install:
	@container=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "sovpadem_web"; else echo "sovpadem_web_dev"; fi); \
	echo "Installing composer dependencies..."; \
	docker exec $$container bash -c "cd /var/www && composer install"

composer-install-dev:
	@echo "Installing composer dependencies in development environment..."
	@docker exec sovpadem_web_dev bash -c "cd /var/www && composer install"

composer-install-prod:
	@echo "Installing composer dependencies in production environment..."
	@docker exec sovpadem_web bash -c "cd /var/www && composer install --no-dev --optimize-autoloader"

# Миграции
migrate:
	@container=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "sovpadem_web"; else echo "sovpadem_web_dev"; fi); \
	env=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "production"; else echo "development"; fi); \
	echo "Running migrations in $$env environment..."; \
	docker exec $$container bash -c "cd /var/www && vendor/bin/phinx migrate -e $$env"

migrate-dev:
	@echo "Running migrations in development environment..."
	@docker exec sovpadem_web_dev bash -c "cd /var/www && vendor/bin/phinx migrate -e development"

migrate-prod:
	@echo "Running migrations in production environment..."
	@docker exec sovpadem_web bash -c "cd /var/www && vendor/bin/phinx migrate -e production"

create-migration:
	@if [ -z "$(name)" ]; then \
		echo "Error: Migration name is required. Usage: make create-migration name=MigrationName"; \
		exit 1; \
	fi; \
	container=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "sovpadem_web"; else echo "sovpadem_web_dev"; fi); \
	docker exec $$container bash -c "cd /var/www && vendor/bin/phinx create $(name)"

rollback:
	@container=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "sovpadem_web"; else echo "sovpadem_web_dev"; fi); \
	env=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "production"; else echo "development"; fi); \
	echo "Rolling back migrations in $$env environment..."; \
	docker exec $$container bash -c "cd /var/www && vendor/bin/phinx rollback -e $$env"

seed:
	@container=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "sovpadem_web"; else echo "sovpadem_web_dev"; fi); \
	env=$$(if [ -f .env ] && grep -q "APP_ENV=prod" .env; then echo "production"; else echo "development"; fi); \
	echo "Running seeders in $$env environment..."; \
	docker exec $$container bash -c "cd /var/www && vendor/bin/phinx seed:run -e $$env"