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

## Getting started

### Prerequirements

1. Copy .env file from example file

```bash
cp -pv .env.example .env
```

2. Start ngrok or other tunneling service, it should provide you with a publicly accessible URL running over HTTPS

In this starter we use cloudpub (ngrok like) for backend and frontend development.

3. Create new Bitrix24 portal and create new local application

You can create new application in Bitrix24 portal → Left menu → Developer Resources → Other → Local Applications

4. Fill local applicaiton parameters in «new local application page» on your Bitrix24 portal

Parameters:

- Server (yes)
- Your handler path (enter your tunneling service url)
- Initial Installation path (enter your tunneling service url + `/install`)
- Menu item text (your application name)
- Assign permissions (scope): `crm, user_brief, placement`, its minimum permissions for work demo application

### Detailed step-by-step instruction if You work with PHP backend on macOS

1. Go to `docker-compose.yml` and change `image: cloudpub/cloudpub:latest` to `image: cloudpub/cloudpub:latest-arm64`
   in containers:

- `cloudpub-php`
- `cloudpub-front`

2. Enter your cloudpub api-key in `.env` file

`CLOUDPUB_TOKEN`

3. Start dev-containers

```bash
make dev-php
```

4. Update backend dependencies and check if everything works fine

```bash
make composer-update
```

5. Found URLs for frontend and backend applications from your cloudpub or ngrok tunneling app

Example output in console for cloudpub:

```bash

...
cloudpubApiPhp  | http://frontend:3000 -> https://inanely-muscular-wagtail.cloudpub.com:443
cloudpubApiPhp  | http://api-php:8000 -> https://furtively-awake-rhea.cloudpub.com:443
...

```

Remember it.

6. Set this URL in root `.env` file
   This URLs are used in your frontend and backend applications:

- VIRTUAL_HOST - this is Your FRONTEND application, it should be a URL that you enter in the application settings in Bitrix24
- NUXT_PUBLIC_API_URL - this is Your BACKEND application, Bitrix24 should not send requests to it directly, all events and requests from Bitrix24 go to
  VIRTUAL_HOST

```dotenv
VIRTUAL_HOST=https://inanely-muscular-wagtail.cloudpub.com

NUXT_PUBLIC_API_URL=https://furtively-awake-rhea.cloudpub.com
```

7. Enter them in local application parameters in Bitrix24 portal

Get this frontend url and enter it in local application parameters in Bitrix24 portal:

- Your handler path: `https://inanely-muscular-wagtail.cloudpub.com`
- Initial Installation path: `https://inanely-muscular-wagtail.cloudpub.com/install`

after You click on save button in local application parameters in Bitrix24 portal, You will see your local application parameters:

**Attention! Your parameters will be different**

example:

- Application ID (client_id): `local.6901c_xxxxxxx`
- Application key (client_secret): `vXpv64o_xxxxxxx`

## API endpoints

### General principles

All requests (except `/api/install`, `/api/getToken`) pass JWT in the headers.

Example:

```javascript
const {data, error} = await $fetch('/api/protected-route', {
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
  timestamp: 1760611967
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
  - REFRESH_ID: string
  - member_id: string
  - user_id: number
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
  -d '{"AUTH_ID":"27exx66815","AUTH_EXPIRES":3600,"REFRESH_ID":"176xxxe","member_id":"a3xxx22","user_id":"1","PLACEMENT":"DEFAULT","PLACEMENT_OPTIONS":"{"any":"6\/"}"}'
```

### `/api/getToken`

Called by the frontend to obtain a JWT token from the backend.

Authorization data from Bitrix24 is passed as input.

The token lifetime is `1 hour`.

**JWT token is not transferred.**

- method `POST`
- params:
  - DOMAIN': string
  - PROTOCOL: number
  - LANG: string
  - APP_SID: string
  - AUTH_ID: string
  - AUTH_EXPIRES: number
  - REFRESH_ID: string
  - member_id: string
  - user_id: number
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
  -d '{"AUTH_ID":"27exx66815","AUTH_EXPIRES":3600,"REFRESH_ID":"176xxxe","member_id":"a3xxx22","member_id":1}'
```
