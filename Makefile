.PHONY: dev prod down clean help

help:
	@echo "Available commands:"
	@echo "  make dev    - Start development environment"
	@echo "  make prod   - Start production environment"
	@echo "  make down   - Stop all containers"
	@echo "  make clean  - Stop and remove all containers, volumes"
	@echo "  make help   - Show this help message"

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