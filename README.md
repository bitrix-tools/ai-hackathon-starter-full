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

## Core Components

**Required scopes**: `crm, user_brief, pull, placement, userfieldconfig`

Path for install:
* **app:** https://amply-awake-dace.cloudpub.ru/
* **install:** https://amply-awake-dace.cloudpub.ru/install


## Structure

```text
starter-kit/
├── frontend/               # Nuxt frontend
├── backends/               # All backends
│   ├── php/
│   │   ├── api/            # API server
│   │   └── shared/         # Common code
│   ├── python/
│   │   ├── api/
│   │   └── shared/
│   └── node/
│       ├── api/
│       └── shared/
├── infrastructure/
│   ├── nginx/
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

## API endpoints

### General principles
All requests (except `/api/install`, `/api/getToken`) pass JWT in the headers.

Example:
```javascript
const { data, error } = await $fetch('/api/protected-route', {
  method: 'GET',
  headers: {
    Authorization: `Bearer ${soneJWT}`
  }
});
```

The server checks every request (except `/api/install`, `/api/getToken`) for a valid JWT token.

The server returns a response in `json` format.

If an error occurs, the server sets the response code to `401` or `404` or `500` and returns an error description in the following format:
  - error: `string`

Return example:
```json
{
  error: 'Internal server error'
}
```

### `/api/health`

Specifies the status of the backend.

- method `GET`
- params: not
- response:
  - status: `string`
  - backend: `string`
  - timestamp: `number`

Return example:
```json
{
  status: 'healthy',
  backend: 'php',
  timestamp: 1760611967,
}
```

Test
```bash
curl http://localhost:8000/api/health
```

### `/api/enum`

Returns an enumeration of options.

- method `GET`
- params: not
- response: `string[]`

Return example:
```json
[
  'option 1',
  'option 2',
  'option 3'
]
```

Test
```bash
curl http://localhost:8000/api/list
```

### `/api/list`

Returns a list of elements.

- method `GET`
- params: not
- response: `string[]`

Return example:
```json
[
  'element 1',
  'element 2',
  'element 3'
]
```

Test
```bash
curl http://localhost:8000/api/list
```

### `/api/install`

Called from the frontend client when the application is installed.

JWT token is not transferred.

- method `POST`
- params:
  - DOMAIN': string
  - PROTOCOL: number
  - LANG: string
  - APP_SID: string
  - AUTH_ID: string
  - AUTH_EXPIRES: number
  - REFRESH_TOKEN: string
  - member_id: string
  - PLACEMENT: string
  - PLACEMENT_OPTIONS: Record<string: any>
- response:
  - message: `string`

Return example:
```json
{
  message: 'All success'
}
```

Test
```bash
curl -X POST http://localhost:8000/api/install \
  -H "Content-Type: application/json" \
  -d '{"AUTH_ID":"27exx66815","AUTH_EXPIRES":3600,"REFRESH_TOKEN":"176xxxe","member_id":"a3xxx22","PLACEMENT":"DEFAULT","PLACEMENT_OPTIONS":"{"any":"6\/"}"}'
```

### `/api/getToken`

Called by the frontend to obtain a JWT token from the backend.

Authorization data from Bitrix24 is passed as input.

The token lifetime is `1 hour`.

**JWT token is not transferred.**

- method `POST`
- params:
  - APP_SID: string
  - AUTH_ID: string
  - AUTH_EXPIRES: number
  - member_id: string
- response:
  - token: `string`

Return example:
```json
{
  token: 'AIHBdxxxLLL'
}
```

Test
```bash
curl -X POST http://localhost:8000/api/getToken \
  -H "Content-Type: application/json" \
  -d '{"AUTH_ID":"27exx66815","AUTH_EXPIRES":3600,"member_id":"a3xxx22"}'
```
