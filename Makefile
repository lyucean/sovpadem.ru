.PHONY: dev prod down clean clean-all rebuild rebuild-dev rebuild-prod help
help:
	@echo "Available commands:"
	@echo "  make dev         - Start development environment"
	@echo "  make prod        - Start production environment"
	@echo "  make down        - Stop all containers"
	@echo "  make clean       - Stop and remove all containers, volumes"
	@echo "  make rebuild     - Clean all and restart the last used environment"
	@echo "  make rebuild-dev - Clean all and restart development environment"
	@echo "  make rebuild-prod - Clean all and restart production environment"
	@echo "  make help        - Show this help message"

dev:
	docker compose --profile dev up -d
	@echo "Development environment started at http://localhost:8080"

prod:
	docker compose --profile prod up -d
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
