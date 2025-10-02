# @bitrix24/starter-kit

This starter provides:

- Three backend options via profiles
- Workers for background tasks
- RabbitMQ for asynchronous work
- Nginx for production
- External logs and volumes
- Ready-to-use SDKs and common utilities
- Makefile for convenience
- Documented API endpoints

Developers can easily add their own backends by simply creating a folder in backends/ with the appropriate structure.

## Structure

```text
starter-kit/
├── frontend/               # Nuxt frontend
├── backends/               # All backends
│   ├── php/
│   │   ├── api/            # API server
│   │   ├── worker/         # Worker for background tasks
│   │   └── shared/         # Common code
│   ├── python/
│   │   ├── api/
│   │   ├── worker/
│   │   └── shared/
│   └── node/
│       ├── api/
│       ├── worker/
│       └── shared/
├── infrastructure/
│   ├── nginx/
│   ├── rabbitmq/
│   └── database/
└── logs/                    # Logs outside containers
```

## Usage:

```bash
# Edit .env if necessary
cp .env.example .env

# Development with PHP backend + worker
make dev-php

# Development with Python backend + worker
make dev-python

# Development with Node.js backend + worker
make dev-node

# Stop
make down

# Production with PHP
make prod-PHP

# Production with Python
make prod-python

# Production with Node.js
make prod-node

# Only the db + frontend (without the backend - for testing)
COMPOSE_PROFILES= docker-compose up database frontend

# Full stack
COMPOSE_PROFILES=php,worker docker-compose up -d
```

## API endpoints for testing
```text
# bash check
curl http://localhost:8000/health

# Get users
curl http://localhost:8000/api/users

# Create user (send to queue)
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@example.com"}'

# Run a background task
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{"task":"process_data"}'
```
