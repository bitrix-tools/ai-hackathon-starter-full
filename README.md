# @bitrix24/ai-hackathon-starter-full

This framework provides:

* Isolated containers for each service
* Support for three backends via environment variables
* Dev/Prod modes via Docker profiles
* PostgreSQL database
* Ready-made configurations for a quick start
* Each backend can be easily extended with models, routes, and business logic.

## Usage:

1. Clone and configure:

```bash
# Edit .env if necessary
cp .env.example .env
```

2. Run in dev mode:

```bash
# With Node.js backend (default)
make dev

# Or a specific backend
make dev-python
make dev-php
make dev-node
```

3. Run in prod mode:

```bash
make prod
```

4. Stop

```bash
make down
```
## API endpoints that must be present in the backends:
```text
GET /api/health - Health check
GET /api/users - Get all users
POST /api/users - Create user
```

The frontend automatically adapts to any of the three backends and provides a consistent interface for working with the application.

### PHP Backend Testing
After launching, check the endpoints:

bash
```# Health check
curl http://localhost:3000/api/health

# Get all users
curl http://localhost:3000/api/users

# Create user
curl -X POST http://localhost:3000/api/users \
-H "Content-Type: application/json" \
-d '{"name":"John Doe","email":"john@example.com"}'
```
