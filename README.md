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
