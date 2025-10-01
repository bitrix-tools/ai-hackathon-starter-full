.PHONY: dev prod build down clean

dev:
	@echo "Starting development environment with $(BACKEND_TYPE) backend"
	docker-compose --env-file .env up --build

prod:
	@echo "Starting production environment"
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

down:
	docker-compose down

clean:
	docker-compose down -v
	docker system prune -f

# Specific backend targets
dev-python:
	BACKEND_TYPE=python make dev

dev-php:
	BACKEND_TYPE=php make dev

dev-node:
	BACKEND_TYPE=node make dev
