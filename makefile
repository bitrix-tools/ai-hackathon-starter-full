.PHONY: init-network init-nginxproxy dev prod dev-php dev-python dev-node prod-php prod-python prod-node status down down-all logs clean

# Init
init-network:
	@echo "Starting network"
	docker network create proxy-net

init-nginxproxy:
	@echo "Starting nginx proxy"
	docker compose -f docker compose.server.yml up -d

# Development
dev:
	@echo "Starting dev php,worker"
	COMPOSE_PROFILES=php,worker docker compose up --build

dev-php:
	@echo "Starting dev php,worker"
	COMPOSE_PROFILES=php,worker docker compose up --build

dev-python:
	@echo "Starting dev python,worker"
	COMPOSE_PROFILES=python,worker docker compose up --build

dev-node:
	@echo "Starting dev node,worker"
	COMPOSE_PROFILES=node,worker docker compose up --build

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

down:
	docker compose down

down-all:
	docker compose down
	docker compose -f docker compose.server.yml down

logs:
	docker compose logs -f

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
