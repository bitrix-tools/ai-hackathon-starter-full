.PHONY: init-network init-nginxproxy prod dev-front dev-php dev-python dev-node prod-php prod-python prod-node status ps down down-all logs logs-nginxproxy clean

# Init
init-network:
	@echo "Starting network"
	docker network create proxy-net

init-nginxproxy:
	@echo "Starting nginx proxy"
	docker compose -f docker-compose.server.yml up -d

# Development
dev-front:
	@echo "Starting frontend"
	COMPOSE_PROFILES= docker compose --env-file .env up frontend --build

dev-php:
	@echo "Starting dev php,worker"
	COMPOSE_PROFILES=php docker compose up --build

# todo restore this: COMPOSE_PROFILES=php,worker docker compose up --build -d
dev-python:
	@echo "Starting dev python,worker"
	COMPOSE_PROFILES=python,worker docker compose up --build -d

dev-node:
	@echo "Starting dev node,worker"
	COMPOSE_PROFILES=node,worker docker compose up --build -d

# Production
prod:
	@echo "Starting prod php,worker environment"
	COMPOSE_PROFILES=php,worker FRONTEND_TARGET=production docker compose up --build -d

prod-php:
	@echo "Starting prod php,worker environment"
	COMPOSE_PROFILES=php,worker FRONTEND_TARGET=production docker compose up --build -d

prod-python:
	@echo "Starting prod python,worker environment"
	COMPOSE_PROFILES=python,worker FRONTEND_TARGET=production docker compose up --build -d

prod-node:
	@echo "Starting prod node,worker environment"
	COMPOSE_PROFILES=node,worker FRONTEND_TARGET=production docker compose up --build -d

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

# RabbitMQ management
rabbitmq-ui:
	open http://localhost:15672
