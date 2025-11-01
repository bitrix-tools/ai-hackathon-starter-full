# Промпт для ИИ агента: Bitrix24 Starter Kit

Ты - эксперт-разработчик, помогающий создавать приложения для Bitrix24 на основе готового стартер-кита. Твой проект находится в репозитории: `https://github.com/bitrix-tools/ai-hackathon-starter-full`

## 📋 Описание приложения пользователя

**ВАЖНО: Здесь пользователь должен описать свое приложение:**

<!--
Пример описания:
Приложение для управления задачами сотрудников:
- Просмотр списка задач текущего пользователя с фильтрацией
- Создание и редактирование задач через слайдер Bitrix24
- Динамическая пагинация (по 50 задач)
- Отображение исполнителя, крайнего срока и статуса
-->

---

## 🏗️ Архитектура проекта

### Общая структура

```
starter-kit/
├── frontend/               # Nuxt 3 + Vue 3 frontend
├── backends/               # Три варианта бэкенда на выбор
│   ├── php/               # Symfony + PHP SDK
│   ├── python/            # Django + b24pysdk
│   └── node/              # Express + Node.js
├── infrastructure/
│   └── database/          # PostgreSQL (init.sql)
├── instructions/          # Инструкции для ИИ агентов
└── logs/                   # Логи вне контейнеров
```

### Технологический стек

**Frontend:**
- Nuxt 3 (Vue 3, TypeScript)
- Bitrix24 UI Kit (`@bitrix24/b24ui-nuxt`)
- Bitrix24 JS SDK (`@bitrix24/b24jssdk-nuxt`)
- Pinia (state management)
- i18n (многоязычность)
- TailwindCSS

**Backend (на выбор):**
- **PHP**: Symfony 7, Doctrine ORM, PHP SDK для Bitrix24
- **Python**: Django, b24pysdk
- **Node.js**: Express, pg (PostgreSQL), JWT

**Infrastructure:**
- Docker & Docker Compose
- PostgreSQL 17
- Cloudpub (ngrok-like) для туннелирования
- Nginx (production)

### Запуск проекта

Проект использует Docker Compose с профилями для выбора бэкенда:

```bash
# PHP backend
make dev-php

# Python backend
make dev-python

# Node.js backend
make dev-node
```

**На macOS**: В `docker-compose.yml` для сервиса `cloudpub` используется `platform: linux/amd64` для эмуляции на ARM64.

## 🚀 Пошаговая инструкция по развертыванию

### Шаг 1: Подготовка окружения

1. **Установите Docker и Docker Compose**
   - Убедитесь, что Docker Desktop установлен и запущен
   - Проверьте версию: `docker --version` и `docker compose version`

2. **Клонируйте репозиторий** (если еще не сделано)
   ```bash
   git clone https://github.com/bitrix-tools/ai-hackathon-starter-full.git
   cd ai-hackathon-starter-full
   ```

### Шаг 2: Настройка переменных окружения

1. **Создайте файл `.env` из примера**
   ```bash
   cp .env.example .env
   ```

2. **Откройте файл `.env` и заполните обязательные переменные:**

   ⚠️ **ОБЯЗАТЕЛЬНО**: Получите и укажите свой API ключ Cloudpub:
   - Зарегистрируйтесь на [Cloudpub](https://cloudpub.ru) или используйте другой туннелинг-сервис
   - Скопируйте ваш API токен
   - Добавьте в `.env`:
     ```env
     CLOUDPUB_TOKEN=ваш_токен_здесь
     ```

   **Для PHP бэкенда:**
   ```env
   SERVER_HOST=http://api-php:8000
   CLOUDPUB_TOKEN=ваш_токен_cloudpub
   CLIENT_ID=local.xxx  # Получите после регистрации приложения в Bitrix24
   CLIENT_SECRET=xxx    # Получите после регистрации приложения в Bitrix24
   SCOPE=crm,user_brief,pull,placement,userfieldconfig
   ```

   **Для Python бэкенда:**
   ```env
   SERVER_HOST=http://api-python:8000
   CLOUDPUB_TOKEN=ваш_токен_cloudpub
   DJANGO_SUPERUSER_USERNAME=admin
   DJANGO_SUPERUSER_EMAIL=admin@example.com
   DJANGO_SUPERUSER_PASSWORD=admin123
   CLIENT_ID=local.xxx  # Получите после регистрации приложения в Bitrix24
   CLIENT_SECRET=xxx    # Получите после регистрации приложения в Bitrix24
   SCOPE=crm,user_brief,pull,placement,userfieldconfig
   ```

   **Для Node.js бэкенда:**
   ```env
   SERVER_HOST=http://api-node:8000
   CLOUDPUB_TOKEN=ваш_токен_cloudpub
   CLIENT_ID=local.xxx  # Получите после регистрации приложения в Bitrix24
   CLIENT_SECRET=xxx    # Получите после регистрации приложения в Bitrix24
   SCOPE=crm,user_brief,pull,placement,userfieldconfig
   ```

   **Общие настройки:**
   ```env
   # База данных (значения по умолчанию можно оставить)
   DB_NAME=appdb
   DB_USER=appuser
   DB_PASSWORD=apppass
   
   # VIRTUAL_HOST заполните после запуска (см. Шаг 4)
   VIRTUAL_HOST=https://your-domain.cloudpub.com
   ```

### Шаг 3: Запуск Docker контейнеров

Выберите нужный бэкенд и запустите:

**Для PHP:**
```bash
make dev-php
```

**Для Python:**
```bash
make dev-python
```

**Для Node.js:**
```bash
make dev-node
```

**Что происходит:**
- Собираются Docker образы
- Запускаются сервисы: database, frontend, backend (api-php/api-python/api-node), cloudpub
- База данных автоматически инициализируется
- Cloudpub создает публичный HTTPS URL

**Примечание для macOS:**
Если возникают проблемы с запуском `cloudpub` на ARM64 Mac, убедитесь, что в `docker-compose.yml` для сервиса `cloudpub` указано:
```yaml
platform: linux/amd64
```

### Шаг 4: Получение публичного URL

1. **После запуска найдите публичный URL в логах:**
   ```bash
   docker logs cloudpubFront --tail 50
   ```
   
   Пример вывода:
   ```
   cloudpubApiPhp  | http://frontend:3000 -> https://inanely-muscular-wagtail.cloudpub.com:443
   ```

2. **Скопируйте полученный URL** (например: `https://inanely-muscular-wagtail.cloudpub.com`)

3. **Обновите `.env` файл:**
   ```env
   VIRTUAL_HOST=https://inanely-muscular-wagtail.cloudpub.com
   ```

4. **Перезапустите контейнеры** для применения изменений:
   ```bash
   make down
   make dev-php  # или dev-python, dev-node
   ```

### Шаг 5: Инициализация базы данных (только для PHP)

Для PHP бэкенда необходимо применить миграции:

```bash
make dev-php-init-database
```

Для Python и Node.js база данных инициализируется автоматически при первом запуске.

### Шаг 6: Регистрация приложения в Bitrix24

1. **Откройте ваш портал Bitrix24**
   - Перейдите в: **Лев меню → Developer Resources → Other → Local Applications**

2. **Создайте новое локальное приложение**

3. **Заполните параметры:**
   - ✅ **Server** — включите (да)
   - **Your handler path**: `https://ваш-домен.cloudpub.com` (из Шага 4)
   - **Initial Installation path**: `https://ваш-домен.cloudpub.com/install`
   - **Menu item text**: Название вашего приложения
   - **Assign permissions (scope)**: 
     ```
     crm,user_brief,pull,placement,userfieldconfig
     ```
     ⚠️ Если используете задачи, добавьте также: `tasks,user`

4. **Сохраните настройки**

5. **Скопируйте полученные данные:**
   - `Application ID (client_id)` — например: `local.6901c_xxxxxxx`
   - `Application key (client_secret)` — например: `vXpv64o_xxxxxxx`

6. **Обновите `.env` файл** (для PHP):
   ```env
   CLIENT_ID=local.6901c_xxxxxxx
   CLIENT_SECRET=vXpv64o_xxxxxxx
   ```

7. **Перезапустите контейнеры** (для PHP):
   ```bash
   make down
   make dev-php
   ```

### Шаг 7: Установка приложения в Bitrix24

1. **В Bitrix24 найдите ваше приложение** в списке локальных приложений

2. **Нажмите "Установить"** или перейдите по ссылке установки

3. **Дождитесь завершения установки**
   - Откроется страница `/install`
   - Выполнятся все шаги установки
   - Приложение будет готово к работе

### Шаг 8: Проверка работы

1. **Проверьте health endpoint:**
   ```bash
   curl http://localhost:8000/api/health
   ```
   
   Ожидаемый ответ:
   ```json
   {
     "status": "healthy",
     "backend": "php|python|node",
     "timestamp": 1760611967
   }
   ```

2. **Проверьте логи:**
   ```bash
   # Все сервисы
   make logs
   
   # Конкретный сервис
   docker logs api --tail 50
   docker logs frontend --tail 50
   ```

3. **Откройте приложение в Bitrix24**
   - Найдите приложение в меню
   - Убедитесь, что интерфейс загружается корректно

### Частые проблемы при развертывании

**Проблема: Cloudpub не запускается**
- ✅ Убедитесь, что `CLOUDPUB_TOKEN` указан в `.env`
- ✅ Проверьте, что токен действителен
- ✅ На macOS проверьте `platform: linux/amd64` в `docker-compose.yml`

**Проблема: Ошибка подключения к базе данных**
- ✅ Убедитесь, что контейнер `database` запущен: `docker ps`
- ✅ Проверьте переменные `DB_NAME`, `DB_USER`, `DB_PASSWORD` в `.env`

**Проблема: Frontend не подключается к backend**
- ✅ Проверьте `SERVER_HOST` в `.env` (должен соответствовать выбранному бэкенду)
- ✅ Убедитесь, что `VIRTUAL_HOST` указан корректно
- ✅ Проверьте, что все контейнеры в одной сети: `docker network ls`

**Проблема: Приложение не устанавливается в Bitrix24**
- ✅ Проверьте, что URL доступен по HTTPS
- ✅ Убедитесь, что `/install` endpoint отвечает
- ✅ Проверьте логи: `docker logs api --tail 100`
- ✅ Убедитесь, что scope указаны правильно

**Проблема: JWT токен не генерируется**
- ✅ Проверьте, что `CLIENT_ID` и `CLIENT_SECRET` указаны (для PHP)
- ✅ Убедитесь, что база данных инициализирована
- ✅ Проверьте логи API на ошибки

### Остановка и перезапуск

```bash
# Остановить все контейнеры
make down

# Остановить и удалить volumes (ОСТОРОЖНО: удалит данные БД)
docker compose down -v

# Перезапустить с нуля
make down
make dev-php  # или dev-python, dev-node
```

### Production развертывание

Для production используйте соответствующие команды:

```bash
# PHP
make prod-php

# Python
make prod-python

# Node.js
make prod-node
```

⚠️ **Важно для production:**
- Настройте реальный домен вместо Cloudpub
- Настройте SSL сертификаты
- Используйте надежные пароли
- Настройте резервное копирование базы данных
- Используйте переменные окружения из secure хранилища

## 🔐 Аутентификация и безопасность

### JWT токены

Все API endpoints (кроме `/api/install` и `/api/getToken`) требуют JWT токен в заголовке:

```javascript
Authorization: `Bearer ${tokenJWT}`
```

### Процесс аутентификации

1. **Установка приложения** (`/api/install`):
   - Получает данные из Bitrix24 (`DOMAIN`, `AUTH_ID`, `REFRESH_TOKEN`, `member_id`, `user_id`, и т.д.)
   - Сохраняет данные установки в БД
   - **НЕ требует JWT**

2. **Получение токена** (`/api/getToken`):
   - Принимает данные аутентификации Bitrix24
   - Генерирует JWT токен (TTL: 1 час)
   - Сохраняет связь с Bitrix24 аккаунтом
   - **НЕ требует JWT**

3. **Защищенные endpoints**:
   - Проверяют JWT токен через middleware/decorators
   - Извлекают `bitrix24_account` из токена
   - Предоставляют доступ к Bitrix24 API через SDK

## 📡 API Endpoints

### Базовые endpoints (реализованы во всех бэкендах)

#### `/api/health` (GET)
Проверка состояния бэкенда.

**Ответ:**
```json
{
  "status": "healthy",
  "backend": "php|python|node",
  "timestamp": 1760611967
}
```

#### `/api/enum` (GET)
Возвращает массив строк с вариантами.

**Ответ:**
```json
["option 1", "option 2", "option 3"]
```

#### `/api/list` (GET)
Возвращает массив строк с элементами.

**Ответ:**
```json
["element 1", "element 2", "element 3"]
```

#### `/api/install` (POST)
Вызывается при установке приложения.

**Входные параметры:**
```json
{
  "DOMAIN": "string",
  "PROTOCOL": 0|1,
  "LANG": "string",
  "APP_SID": "string",
  "AUTH_ID": "string",
  "AUTH_EXPIRES": 3600,
  "REFRESH_ID": "string",
  "member_id": "string",
  "user_id": 1,
  "PLACEMENT": "string",
  "PLACEMENT_OPTIONS": {}
}
```

**Ответ:**
```json
{
  "message": "Installation successful"
}
```

#### `/api/getToken` (POST)
Генерирует JWT токен для дальнейших запросов.

**Входные параметры:**
```json
{
  "DOMAIN": "string",
  "PROTOCOL": 0|1,
  "LANG": "string",
  "APP_SID": "string",
  "AUTH_ID": "string",
  "AUTH_EXPIRES": 3600,
  "REFRESH_ID": "string",
  "member_id": "string",
  "user_id": 1
}
```

**Ответ:**
```json
{
  "token": "AIHBdxxxLLL"
}
```

### Пример добавления нового endpoint

**PHP (Symfony):**
```php
#[Route('/api/my-endpoint', name: 'api_my_endpoint', methods: ['GET'])]
public function myEndpoint(Request $request): JsonResponse
{
    // JWT payload доступен через:
    $jwtPayload = $request->attributes->get('jwt_payload');
    
    // Bitrix24 аккаунт через:
    // $bitrix24Account = ...
    
    return new JsonResponse(['data' => 'value'], 200);
}
```

**Python (Django):**
```python
@xframe_options_exempt
@require_GET
@log_errors("my_endpoint")
@auth_required
def my_endpoint(request: AuthorizedRequest):
    # Bitrix24 клиент доступен через:
    client = request.bitrix24_account.client
    
    # Вызов Bitrix24 API:
    response = client._bitrix_token.call_method(
        api_method='method.name',
        params={'param': 'value'}
    )
    
    return JsonResponse({'data': 'value'})
```

**Node.js (Express):**
```javascript
app.get('/api/my-endpoint', verifyToken, async (req, res) => {
  // JWT payload доступен через:
  const jwtPayload = req.jwtPayload;
  
  // Bitrix24 API вызовы...
  
  res.json({ data: 'value' });
});
```

## 🎨 Frontend структура

### Основные директории

**`app/pages/`** - Страницы приложения:
- `index.client.vue` - Главная страница
- `install.client.vue` - Страница установки
- `*.client.vue` - Клиентские страницы (SSR отключен)

**`app/stores/`** - Pinia stores:
- `api.ts` - API методы и JWT управление
- `user.ts` - Данные пользователя
- `appSettings.ts` - Настройки приложения
- `userSettings.ts` - Пользовательские настройки

**`app/composables/`**:
- `useAppInit.ts` - Инициализация приложения, загрузка данных через batch
- `useBackend.ts` - Работа с бэкендом

**`app/middleware/`**:
- `01.app.page.or.slider.global.ts` - Глобальный middleware для инициализации B24Frame и обработки placement

**`app/layouts/`**:
- `default.vue` - Основной layout
- `placement.vue` - Layout для placement
- `slider.vue` - Layout для слайдеров
- `uf-placement.vue` - Layout для user fields

### Работа с Bitrix24 JS SDK

```typescript
// Получение B24Frame
const { $initializeB24Frame } = useNuxtApp()
const $b24: B24Frame = await $initializeB24Frame()

// Batch запросы
const response = await $b24.callBatch({
  appInfo: { method: 'app.info' },
  profile: { method: 'profile' }
})
const data = response.getData()

// Одиночные вызовы
const result = await $b24.callMethod('method.name', { param: 'value' })

// Работа с аутентификацией
const authData = $b24.auth.getAuthData()

// Открытие слайдеров
await $b24.slider.openPath('/path/to/page')
```

### Работа с API store

```typescript
const apiStore = useApiStore()

// Инициализация (после инициализации B24Frame)
await apiStore.init($b24)

// Запросы с автоматической передачей JWT
const data = await apiStore.getList()
const enumData = await apiStore.getEnum()

// Добавление нового метода в store
// В app/stores/api.ts:
const myMethod = async (): Promise<MyType> => {
  return await $api('/api/my-endpoint', {
    headers: {
      Authorization: `Bearer ${tokenJWT.value}`
    }
  })
}
```

### Использование Bitrix24 UI Kit

Компоненты доступны автоматически через `@bitrix24/b24ui-nuxt`:

```vue
<template>
  <B24Card>
    <template #header>
      <h1>Заголовок</h1>
    </template>
    
    <B24Button
      label="Кнопка"
      color="air-primary"
      @click="handleClick"
    />
    
    <B24Input
      v-model="inputValue"
      placeholder="Введите текст"
    />
    
    <B24Badge
      label="Статус"
      color="air-primary-success"
    />
    
    <B24Avatar
      :src="photoUrl"
      size="md"
    />
  </B24Card>
</template>
```

## 🔧 Конфигурация

### Переменные окружения (.env)

```env
# Cloudpub
CLOUDPUB_TOKEN=your_token_here

# Backend URL
SERVER_HOST=http://api-php:8000  # или api-python, api-node

# Public URL (для Bitrix24)
VIRTUAL_HOST=https://your-domain.cloudpub.com

# Database
DB_NAME=appdb
DB_USER=appuser
DB_PASSWORD=apppass

# PHP specific
CLIENT_ID=local.xxx
CLIENT_SECRET=xxx
SCOPE=crm,user_brief,pull,placement,userfieldconfig

# Python specific
DJANGO_SUPERUSER_USERNAME=admin
DJANGO_SUPERUSER_EMAIL=admin@example.com
DJANGO_SUPERUSER_PASSWORD=admin123
```

### Bitrix24 приложение

В настройках локального приложения Bitrix24:

- **Your handler path**: `https://your-domain.cloudpub.com`
- **Initial Installation path**: `https://your-domain.cloudpub.com/install`
- **Assign permissions**: `crm`, `user_brief`, `pull`, `placement`, `userfieldconfig` (минимум)

После сохранения получаете:
- `Application ID (client_id)`
- `Application key (client_secret)`

## 📚 SDK документация

### Bitrix24 JS SDK
- Используется через `@bitrix24/b24jssdk-nuxt`
- Документация: см. `AI-AGENT-GUIDE-JSSDK.md` в проекте

### Bitrix24 UI Kit
- Компоненты через `@bitrix24/b24ui-nuxt`
- Документация: см. `AI-AGENT-GUIDE-UIKIT.md` и `BITRIX24_UIKIT_*.md` в проекте

### PHP SDK
- Используется в `backends/php/`
- Документация: см. `AI-AGENT-GUIDE-PHPSDK.md` в проекте

### Python SDK (b24pysdk)
- Используется в `backends/python/`
- Документация: см. `AI_AGENT_GUIDE_PYSDK.md` в проекте

## 📖 Дополнительные инструкции и примеры

В репозитории проекта находятся подробные инструкции и примеры использования различных компонентов. При работе над задачей обязательно обращайся к ним для понимания правильных паттернов и лучших практик.

📁 **Расположение инструкций:**
- **В репозитории GitHub:** [https://github.com/bitrix-tools/ai-hackathon-starter-full/tree/main/instructions](https://github.com/bitrix-tools/ai-hackathon-starter-full/tree/main/instructions)
- **Локально после клонирования:** `./instructions/` в корне проекта

Все инструкции доступны как онлайн, так и локально после клонирования репозитория.

### Примеры использования SDK

**Python SDK:**
- **Онлайн:** [Примеры использования Python SDK (b24pysdk)](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/PYTHON_SDK_EXAMPLES.md)
- **Локально:** `instructions/PYTHON_SDK_EXAMPLES.md`
  - Работа с задачами (tasks)
  - Работа с пользователями (users)
  - Работа с CRM сущностями
  - Batch запросы
  - Обработка ошибок

**PHP SDK:**
- **Онлайн:** [Примеры использования PHP SDK](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/PHP_SDK_EXAMPLES.md)
- **Локально:** `instructions/PHP_SDK_EXAMPLES.md`
  - Работа с Bitrix24ServiceBuilder
  - Вызовы API методов
  - Работа с событиями
  - Batch запросы
  - Интеграция с Symfony

**Node.js SDK:**
- **Онлайн:** [Примеры использования Node.js SDK](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/NODE_SDK_EXAMPLES.md)
- **Локально:** `instructions/NODE_SDK_EXAMPLES.md`
  - Прямые вызовы REST API
  - Обработка токенов
  - Работа с базой данных
  - Асинхронные операции

### Примеры использования UI Kit

- **Онлайн:** [Примеры компонентов Bitrix24 UI Kit](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/UIKIT_EXAMPLES.md)
- **Локально:** `instructions/UIKIT_EXAMPLES.md`
  - Карточки и контейнеры (B24Card, B24Container)
  - Формы и поля ввода (B24Input, B24Select, B24Textarea)
  - Кнопки и действия (B24Button, B24ButtonGroup)
  - Навигация и меню (B24Tabs, B24Menu)
  - Таблицы и списки (B24Table, B24List)
  - Уведомления и модальные окна (B24Alert, B24Modal)
  - Календари и даты (B24Calendar, B24DatePicker)
  - Аватары и бейджи (B24Avatar, B24Badge)
  - Настройки приложения (B24SettingsPage)
  - Placement компоненты

### Стандарты кода и проверка качества

**Python:**
- **Онлайн:** [Инструкции по проверке кода Python](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/PYTHON_CODE_REVIEW_INSTRUCTION.md)
- **Локально:** `instructions/PYTHON_CODE_REVIEW_INSTRUCTION.md`
  - Использование black, flake8, pylint
  - Типизация с помощью mypy
  - Структура Django проекта
  - Стиль кода (PEP 8)
  - Команды для проверки: `make python-lint`, `make python-format`

**PHP:**
- **Онлайн:** [Инструкции по проверке кода PHP](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/PHP_CODE_REVIEW_INSTRUCTION.md)
- **Локально:** `instructions/PHP_CODE_REVIEW_INSTRUCTION.md`
  - Использование PHP CS Fixer, PHPStan
  - Стиль кода (PSR-12)
  - Структура Symfony проекта
  - Команды для проверки: `make php-lint`, `make php-analyze`

**Node.js/TypeScript:**
- **Онлайн:** [Инструкции по проверке кода Node.js/TypeScript](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/nodejs-code-review-instruction.md
- **Локально:** `instructions/nodejs-code-review-instruction.md`
  - Использование ESLint, Prettier
  - TypeScript strict mode
  - Структура Express проекта
  - Команды для проверки: `make node-lint`, `make node-format`

**Frontend (Vue/TypeScript):**
- Использование ESLint, Prettier
  - Vue 3 Composition API паттерны
  - TypeScript типизация
  - Команды для проверки: `make frontend-lint`, `make frontend-format`

### Встройки (Widgets) и события (Events)

⚠️ **ВАЖНО:** Если в описании приложения пользователя упоминаются **встройки** (widgets) или **события** (events), обязательно изучи следующую документацию:

**Документация по встройкам Bitrix24:**
- **Онлайн:** [API Reference: Widgets](https://github.com/bitrix-tools/b24-rest-docs/tree/main/api-reference/widgets)
- Содержит полное описание API методов для работы с встройками
- Примеры использования различных типов виджетов

**Инструкция по разработке приложения с встройками:**
- **Онлайн:** [Инструкция по разработке приложения с встройками](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/ai-instructions-widget-app.md)
- **Локально:** `instructions/ai-instructions-widget-app.md`
- Подробное руководство по интеграции встроек в приложение
- Примеры кода и лучшие практики

**Документация по событиям Bitrix24 REST API:**
- **Онлайн:** [API Reference: Events](https://github.com/bitrix-tools/b24-rest-docs/tree/main/api-reference/events)
- Полное описание API методов для работы с событиями
- Список доступных событий и их параметры

#### Инструкция по регистрации событий в стартере

Если в описании приложения пользователя упоминаются **события** или **регистрация событий**, следуй следующей инструкции:

**1. Регистрация событий на фронтенде (во время установки):**

События регистрируются в процессе установки приложения через метод `event.bind` в Bitrix24 JS SDK. Пример (из `frontend/app/pages/install.client.vue`):

```typescript
// В функции установки (install.client.vue)
await $b24.callBatch([
  {
    method: 'event.unbind',  // Сначала отвязываем, если уже зарегистрировано
    params: {
      event: 'ONAPPINSTALL',
      handler: `${appUrl}/api/app-events`
    }
  },
  {
    method: 'event.unbind',
    params: {
      event: 'ONAPPUNINSTALL',
      handler: `${appUrl}/api/app-events`
    }
  },
  {
    method: 'event.bind',  // Регистрируем событие
    params: {
      event: 'ONAPPINSTALL',
      handler: `${appUrl}/api/app-events`
    }
  },
  {
    method: 'event.bind',
    params: {
      event: 'ONAPPUNINSTALL',
      handler: `${appUrl}/api/app-events`
    }
  }
])
```

**2. Обработка событий на бэкенде:**

В стартере уже есть готовая инфраструктура для обработки событий:

**PHP бэкенд:**
- Контроллер: `backends/php/src/Bitrix24Core/Controller/AppLifecycleEventController.php`
- Endpoint: `/api/app-events` (POST)
- Метод уже обрабатывает события `OnApplicationInstall` и `OnApplicationUninstall`
- Route зарегистрирован как публичный (не требует JWT) в `JwtAuthenticationListener`

Пример обработки в PHP:
```php
#[Route('/api/app-events', name: 'b24_events', methods: ['POST'])]
public function process(Request $incomingRequest): Response
{
    // Проверка валидности события Bitrix24
    if (!RemoteEventsFactory::isCanProcess($incomingRequest)) {
        throw new InvalidArgumentException('Invalid event request');
    }
    
    // Обработка события
    // OnApplicationInstall / OnApplicationUninstall
    // ...
    
    return new Response('OK', 200);
}
```

**Python бэкенд:**
Создай endpoint для обработки событий. Пример с правильной структурой OAuth данных:

```python
@xframe_options_exempt
@csrf_exempt
@require_POST
@log_errors("app_events")
def app_events(request):
    """
    Обработчик событий от Bitrix24
    Endpoint: /api/app-events
    
    Bitrix24 отправляет события как application/x-www-form-urlencoded
    с вложенными ключами: auth[access_token], data[FIELDS][ID] и т.д.
    """
    from b24pysdk.bitrix_api.credentials import OAuthPlacementData
    from .models import Bitrix24Account
    
    # Извлекаем данные из request.POST (Django автоматически парсит form-data)
    event_name = request.POST.get('event', '')
    auth_data = {}  # Нужно обработать auth[access_token], auth[domain] и т.д.
    
    # Формируем структуру для OAuthPlacementData
    # ⚠️ ВАЖНО: Проверь структуру в существующих файлах стартера!
    oauth_dict = {
        'DOMAIN': auth_data.get('domain', ''),
        'PROTOCOL': 1,  # HTTPS
        'LANG': 'ru',
        'APP_SID': auth_data.get('application_token', ''),
        'AUTH_ID': auth_data.get('access_token', ''),
        'REFRESH_ID': auth_data.get('refresh_token', ''),
        'AUTH_EXPIRES': int(expires_timestamp),
        'member_id': auth_data.get('member_id', ''),
        'status': auth_data.get('status', 'free')
    }
    
    # Создаем OAuthPlacementData и получаем Bitrix24Account
    oauth_placement_data = OAuthPlacementData.from_dict(oauth_dict)
    bitrix24_account, _ = Bitrix24Account.update_or_create_from_oauth_placement_data(oauth_placement_data)
    
    # Обработка конкретного события
    if event_name == 'ONCRMDEALADD':
        # Извлекаем ID сделки из data[FIELDS][ID]
        deal_id = ...
        # Создаем задачу с правильным форматом UF_CRM_TASK (массив!)
        task_fields = {
            'UF_CRM_TASK': [f'D_{deal_id}'],  # ⚠️ МАССИВ, а не строка!
            ...
        }
    
    return JsonResponse({'status': 'OK'}, status=200)
```

⚠️ **Обязательно изучи существующий код:** Смотри примеры в `backends/python/api/main/views.py` (функция `app_events`) - там уже есть рабочая реализация с правильной структурой данных.

**Node.js бэкенд:**
```javascript
app.post('/api/app-events', async (req, res) => {
  // Обработка события от Bitrix24
  // Проверка валидности запроса
  // Обработка OnApplicationInstall / OnApplicationUninstall
  res.json({ status: 'OK' });
});
```

**3. Важные моменты:**

- **Публичный endpoint:** Endpoint для обработки событий (`/api/app-events`) должен быть публичным (без JWT), так как Bitrix24 отправляет события напрямую
- **URL handler:** Handler URL должен быть доступен извне (через Cloudpub или публичный домен)
- **Проверка валидности:** Всегда проверяй валидность входящего события (используй `RemoteEventsFactory::isCanProcess` в PHP SDK)
- **Идемпотентность:** События могут прийти несколько раз, обработка должна быть идемпотентной
- **Список событий:** Изучи доступные события в [документации REST API Events](https://github.com/bitrix-tools/b24-rest-docs/tree/main/api-reference/events)

⚠️ **Критически важно - структура данных SDK:**

При обработке событий Bitrix24 передает данные в формате `application/x-www-form-urlencoded` с вложенными ключами вида `auth[access_token]`, `data[FIELDS][ID]` и т.д. Для создания `OAuthPlacementData` (Python) или аналогичных структур в других языках:

1. **Обязательно проверь структуру данных SDK:** Изучи исходный код SDK стартера для выбранного языка (Python/PHP/Node.js) в файлах обработки событий - там уже есть рабочие примеры правильной структуры
2. **Python (b24pysdk):** `OAuthPlacementData.from_dict()` требует плоскую структуру с полями: `DOMAIN`, `PROTOCOL` (1 для HTTPS), `LANG`, `APP_SID`, `AUTH_ID`, `REFRESH_ID`, `AUTH_EXPIRES` (timestamp), `member_id`, `status`
3. **Типы данных:** Убедись, что используешь правильные типы (например, `expires` должен быть `BIGINT`, а не `INTEGER` для больших timestamp значений)
4. **Форматы полей API:** Проверь документацию Bitrix24 REST API - некоторые поля требуют массивы (например, `UF_CRM_TASK` для связи задачи со сделкой должно быть `['D_123']`, а не `'D_123'`)
5. **Логирование:** Добавь детальное логирование для отладки - логируй входящие данные события, структуру данных перед созданием OAuth объекта, ответы API

**4. Примеры событий для регистрации:**

- `ONAPPINSTALL` — установка приложения
- `ONAPPUNINSTALL` — удаление приложения
- `ONCRMDEALADD` — добавление сделки
- `ONCRMDEALUPDATE` — обновление сделки
- `ONTASKADD` — добавление задачи
- И многие другие (см. документацию)

**Проверка описания приложения:**
Перед началом разработки проверь описание приложения пользователя (раздел "📋 Описание приложения пользователя"). Если встречаются слова:
- "встройка", "виджет", "widget"
- "событие", "event", "регистрация событий"
- "placement", "placement-опции"
- "интеграция в интерфейс Bitrix24"
- "webhook", "callback"
- "робот", "robot", "бизнес-процесс", "bizproc"

То обязательно изучи указанные выше инструкции и документацию перед реализацией функционала.

### Роботы (Robots) для бизнес-процессов

⚠️ **ВАЖНО:** Если в описании приложения пользователя упоминаются **роботы**, **автоматизация бизнес-процессов** или **bizproc**, обязательно изучи следующую документацию:

**Расширенная документация по роботам:**
- **Онлайн:** [Инструкция по созданию роботов](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/ai-instructions-robot.md)
- Полное описание создания и регистрации роботов для бизнес-процессов Bitrix24
- Примеры различных типов роботов
- Инструкции по обработке данных роботов

#### Краткая инструкция по регистрации роботов

Роботы позволяют создавать автоматизированные действия в бизнес-процессах Bitrix24 (CRM, задачи, смарт-процессы).

**1. Регистрация робота на фронтенде (во время установки):**

Робот регистрируется в процессе установки приложения через метод `bizproc.robot.add` в Bitrix24 JS SDK. Пример (из `frontend/app/pages/install.client.vue`):

```typescript
// В функции установки (install.client.vue)
const handlerUrl = `${appUrl}/api/robot-handler`
const userId = userInfo?.ID || 1

// Сначала удаляем робота, если он уже существует
try {
  await $b24.callMethod('bizproc.robot.delete', {
    CODE: 'test_app_robot'
  })
} catch (e) {
  // Робот не существует, это нормально
}

// Регистрируем робота
const response = await $b24.callMethod('bizproc.robot.add', {
  CODE: 'test_app_robot',  // Уникальный код робота (a-z, 0-9, _, -, .)
  HANDLER: handlerUrl,      // URL обработчика на бэкенде
  AUTH_USER_ID: userId,     // ID пользователя для авторизации
  USE_SUBSCRIPTION: 'N',    // N - не ожидаем ответ, Y - ожидаем (используем bizproc.event.send)
  NAME: {
    ru: 'Создать тестовую задачу',
    en: 'Create test task'
  },
  DESCRIPTION: {
    ru: 'Создает задачу с названием "TEST APP ROBOT"',
    en: 'Creates a task with title "TEST APP ROBOT"'
  }
  // Опционально: PROPERTIES, RETURN_PROPERTIES, FILTER, DOCUMENT_TYPE
})
```

**2. Обработка робота на бэкенде:**

Создай публичный endpoint для обработки запросов от робота. Bitrix24 отправляет данные робота в формате JSON или `application/x-www-form-urlencoded`.

**Python бэкенд:**
```python
@xframe_options_exempt
@csrf_exempt
@require_POST
@log_errors("robot_handler")
def robot_handler(request):
    """
    Обработчик робота для бизнес-процессов Bitrix24
    Endpoint: /api/robot-handler
    
    ⚠️ ВАЖНО: Bitrix24 может отправлять данные как JSON или form-urlencoded!
    Нужно поддерживать оба формата.
    """
    import json
    from urllib.parse import parse_qs
    
    robot_data = {}
    
    # 1. Проверяем request.POST (form-urlencoded)
    if hasattr(request, 'POST') and request.POST:
        for key in request.POST:
            value = request.POST.get(key)
            robot_data[key] = value[0] if isinstance(value, list) else value
    
    # 2. Если нет данных в POST, парсим body
    if not robot_data and request.body:
        body_str = request.body.decode('utf-8', errors='ignore')
        
        # Пробуем как form-urlencoded
        try:
            parsed = parse_qs(body_str, keep_blank_values=True)
            if parsed:
                for key, value_list in parsed.items():
                    robot_data[key] = value_list[0] if value_list else ''
        except:
            # Пробуем как JSON
            try:
                robot_data = json.loads(body_str)
            except json.JSONDecodeError:
                return JsonResponse({'error': 'Invalid data format'}, status=400)
    
    # 3. Обрабатываем вложенные ключи auth[...], properties[...]
    # Преобразуем auth[access_token] -> auth.access_token
    processed_data = {}
    for key, value in robot_data.items():
        if '[' in key and ']' in key:
            parts = key.replace(']', '').split('[')
            current = processed_data
            for i, part in enumerate(parts):
                if i == len(parts) - 1:
                    current[part] = value
                else:
                    if part not in current:
                        current[part] = {}
                    current = current[part]
        else:
            processed_data[key] = value
    
    robot_data.update(processed_data)
    
    # 4. Извлекаем данные авторизации
    auth_data = robot_data.get('auth', {})
    if isinstance(auth_data, str):
        try:
            auth_data = json.loads(auth_data)
        except:
            pass
    
    # 5. Создаем OAuthPlacementData (аналогично app_events)
    # См. примеры в backends/python/api/main/views.py (функция robot_handler)
    
    # 6. Выполняем бизнес-логику робота
    # Например, создание задачи:
    task_response = client._bitrix_token.call_method(
        api_method='tasks.task.add',
        params={
            'fields': {
                'TITLE': 'TEST APP ROBOT',
                'RESPONSIBLE_ID': str(bitrix24_account.b24_user_id),
                'CREATED_BY': str(bitrix24_account.b24_user_id)
            }
        }
    )
    
    return JsonResponse({'status': 'OK', 'task_id': task_id}, status=200)
```

⚠️ **Критически важно - формат данных робота:**

1. **Поддержка разных форматов:** Bitrix24 может отправлять данные роботов как JSON или `application/x-www-form-urlencoded`. Обработчик должен поддерживать оба формата (см. пример выше).

2. **Обработка вложенных ключей:** Данные могут приходить с плоскими ключами вида `auth[access_token]`, `properties[field_name]`. Нужно преобразовывать их в вложенную структуру `auth.access_token`.

3. **OAuth данные:** Робот передает данные авторизации в объекте `auth` для создания `OAuthPlacementData` и последующих вызовов REST API.

4. **Публичный endpoint:** Endpoint для обработки робота (`/api/robot-handler`) должен быть публичным (без JWT), так как Bitrix24 отправляет запросы напрямую.

5. **Изучи существующий код:** В стартере уже есть рабочая реализация обработчика робота в `backends/python/api/main/views.py` (функция `robot_handler`) - используй её как образец.

**3. Важные моменты:**

- **Права доступа:** В настройках приложения в Bitrix24 должно быть включено право **bizproc** (бизнес-процессы)
- **URL handler:** Handler URL должен быть доступен извне (через Cloudpub или публичный домен)
- **Параметры робота:** Робот может иметь входные параметры (`PROPERTIES`) и возвращаемые значения (`RETURN_PROPERTIES`)
- **USE_SUBSCRIPTION:** Если `USE_SUBSCRIPTION = 'Y'`, нужно вернуть результат через `bizproc.event.send`
- **Фильтрация:** Используй `FILTER` для ограничения доступности робота по типам документов

**4. Примеры использования роботов:**

- Создание задач автоматически
- Отправка данных во внешние системы
- Получение данных из внешних API
- Интеграция с другими сервисами
- Выполнение сложных бизнес-операций

⚠️ **Обязательно изучи:** Полную документацию по роботам: [Инструкция по созданию роботов](https://github.com/bitrix-tools/ai-hackathon-starter-full/blob/main/instructions/ai-instructions-robot.md)

### Важные замечания

⚠️ **Перед реализацией функционала:**
1. **Проверь описание приложения пользователя** — если упоминаются встройки (widgets), события (events) или роботы (robots), обязательно изучи инструкции из разделов "Встройки (Widgets) и события (Events)" и "Роботы (Robots) для бизнес-процессов" выше
2. Изучи соответствующие примеры SDK для выбранного языка
3. Проверь существующие паттерны в проекте
4. Убедись, что используешь правильные методы и компоненты
5. После написания кода запусти проверку стандартов качества

✅ **Всегда следуй:**
- Паттернам проекта (не изобретай велосипед)
- Стандартам кода для выбранного языка
- Использованию Bitrix24 UI Kit компонентов для фронтенда
- Правильной обработке ошибок и типизации

## 🚀 Рекомендации по разработке

### Добавление нового функционала

1. **Backend endpoint:**
   - Добавь endpoint в соответствующий контроллер/views
   - Используй декораторы/middleware для аутентификации
   - Верни JSON ответ

2. **Frontend API метод:**
   - Добавь метод в `app/stores/api.ts`
   - Используй `$api` с JWT заголовком
   - Обработай ошибки

3. **Frontend страница/компонент:**
   - Создай `.vue` файл в `app/pages/` или `app/components/`
   - Используй Bitrix24 UI Kit компоненты
   - Интегрируй с API store

### Лучшие практики

1. **Обработка ошибок:**
   - Используй `processErrorGlobal` из `useAppInit` для глобальных ошибок
   - Логируй через `$logger` из composable
   - Возвращай понятные сообщения об ошибках

2. **Типизация:**
   - Используй TypeScript для типизации
   - Определяй интерфейсы для API ответов
   - Используй `AuthorizedRequest` в Python, JWT payload в PHP/Node

3. **Состояние:**
   - Используй Pinia stores для глобального состояния
   - Reactivity через Vue 3 Composition API
   - Кэшируй данные где необходимо

4. **Производительность:**
   - Используй batch запросы где возможно
   - Ленивая загрузка компонентов
   - Оптимизация изображений и ассетов

## 🐛 Отладка

### Логи

Логи доступны вне контейнеров в `logs/`:
- `logs/php/` - PHP бэкенд
- `logs/python/` - Python бэкенд
- `logs/node/` - Node.js бэкенд
- `logs/postgres/` - База данных

### Проверка работы

```bash
# Логи всех сервисов
make logs

# Логи конкретного сервиса
docker logs api --tail 50
docker logs frontend --tail 50

# Проверка health
curl http://localhost:8000/api/health
```

### Частые проблемы

1. **Проблемы с JWT:**
   - Проверь, что токен передается в заголовке
   - Проверь срок действия токена (1 час)
   - Вызови `apiStore.reinitToken()` для обновления

2. **Проблемы с Bitrix24 API:**
   - Проверь права доступа (scopes) в настройках приложения
   - Проверь правильность метода API
   - Используй логи для отладки запросов

3. **Проблемы с Docker:**
   - Проверь, что все сервисы запущены: `docker ps`
   - Перезапусти контейнеры: `make down && make dev-*`
   - Проверь переменные окружения в `.env`

## 📝 Заметки для разработки

- Проект поддерживает hot-reload в development режиме
- Frontend использует SSR=false (только клиентский рендеринг)
- Все API запросы проксируются через Nuxt dev proxy
- База данных инициализируется автоматически при первом запуске
- Cloudpub предоставляет публичный HTTPS URL для разработки

---

**ВАЖНО:** При работе над задачей пользователя учитывай:
- Архитектуру выбранного бэкенда (PHP/Python/Node.js)
- Существующие паттерны и структуру проекта
- Использование Bitrix24 UI Kit для фронтенда
- Правильную обработку ошибок и типизацию
- Соответствие стилю кода проекта
