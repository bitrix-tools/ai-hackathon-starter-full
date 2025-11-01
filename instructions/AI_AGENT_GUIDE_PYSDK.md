# Инструкция для ИИ-агентов: Bitrix24 Python SDK (B24PySDK)

> **Быстрый старт:** Если у пользователя возникла ошибка, сначала проверьте [Типичные проблемы](#типичные-проблемы-и-решения), затем следуйте [алгоритму действий](#7-алгоритм-действий-при-ошибке). Для поиска классов используйте [таблицы ссылок](#2-ключевые-классы-и-файлы-sdk).

## Краткая сводка

**B24PySDK** — официальный Python SDK для работы с REST API Bitrix24.

**Ключевые возможности:**
- Аутентификация через OAuth-токены (`BitrixToken`, `BitrixApp`) или вебхуки (`BitrixWebhook`)
- Строгая типизация и type hints для всех методов API
- Отложенные вызовы API (deferred execution)
- Эффективная пагинация (`.as_list()`, `.as_list_fast()`)
- Пакетные запросы (batch) до 50 команд (`.call_batch()`) и множественные батчи (`.call_batches()`)
- Автоматическое обновление токенов при истечении срока действия
- Поддержка Python 3.9+

**Архитектура:**
- [`Client`](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/_client.py) — точка входа для всех API-вызовов
- Scopes: `client.crm`, `client.user`, `client.department`, `client.socialnetwork`
- Классы аутентификации: `BitrixWebhook`, `BitrixToken`, `BitrixApp`, `BitrixTokenLocal`, `BitrixAppLocal`

---

## Памятка по установке

### Требования
- Python 3.9 или выше
- Зависимости: `requests>=2.32.4`, `psygnal`, `uuid6`

### Установка из PyPI
```bash
pip install b24pysdk
```

### Проверка версии
```python
from b24pysdk import __version__
print(__version__)  # 0.1.0a1
```

---

## Основные требования по составлению приложений

### 1. Инициализация клиента

**Вариант 1: Вебхук** (для локальных интеграций)
```python
from b24pysdk import BitrixWebhook, Client

# Для URL: https://example.bitrix24.com/rest/123/abc123def456/
bitrix_token = BitrixWebhook(
    domain="example.bitrix24.com",
    auth_token="123/abc123def456"
)
client = Client(bitrix_token)
```

**Вариант 2: OAuth-приложение** (для коммерческих приложений)
```python
from b24pysdk import BitrixToken, BitrixApp, Client

bitrix_app = BitrixApp(
    client_id="app.code.12345",
    client_secret="secret_key"
)

bitrix_token = BitrixToken(
    domain="example.bitrix24.com",
    auth_token="access_token_here",
    refresh_token="refresh_token_here",  # опционально
    bitrix_app=bitrix_app
)

client = Client(bitrix_token)
```

**Вариант 3: Локальное OAuth-приложение**
```python
from b24pysdk import BitrixTokenLocal, BitrixAppLocal, Client

bitrix_app = BitrixAppLocal(
    domain="example.bitrix24.com",
    client_id="local.app.12345",
    client_secret="local_secret"
)

bitrix_token = BitrixTokenLocal(
    auth_token="access_token",
    refresh_token="refresh_token",
    bitrix_app=bitrix_app
)

client = Client(bitrix_token)
```

### 2. Вызов методов API

Все методы Bitrix24 REST API доступны через scopes в клиенте.

**Примеры:**
```python
# CRM Deals
request = client.crm.deal.list()
request = client.crm.deal.get(bitrix_id=123)
request = client.crm.deal.add(fields={"TITLE": "Новая сделка"})
request = client.crm.deal.update(bitrix_id=123, fields={"STAGE_ID": "WON"})
request = client.crm.deal.delete(bitrix_id=123)

# CRM Contacts
request = client.crm.contact.list()
request = client.crm.contact.get(bitrix_id=456)

# Users
request = client.user.get(bitrix_id=1)
```

### 3. Получение результатов

SDK использует отложенные вызовы. Для выполнения запроса обращайтесь к свойству `.result`:

```python
request = client.crm.deal.get(bitrix_id=123)
deal = request.result  # Здесь выполняется реальный API-вызов
print(deal["TITLE"])

# Информация о времени выполнения
print(f"Выполнено за {request.time.duration} секунд")
```

### 4. Работа со списками

**По умолчанию** `.list()` возвращает до 50 записей:
```python
request = client.crm.deal.list()
deals = request.result  # До 50 записей
```

**Получить все записи** через `.as_list()`:
```python
request = client.crm.deal.list()
all_deals = request.as_list().result  # Все записи списком
```

**Эффективная пагинация** для больших датасетов через `.as_list_fast()`:
```python
request = client.crm.deal.list()
deals_generator = request.as_list_fast().result  # Возвращает генератор

for deal in deals_generator:
    print(deal["TITLE"])
```

**Фильтрация и сортировка:**
```python
request = client.crm.deal.list(
    select=["ID", "TITLE", "STAGE_ID"],
    filter={
        "CATEGORY_ID": 0,
        ">OPPORTUNITY": 10000
    },
    order={"TITLE": "ASC"}
)
```

### 5. Пакетные запросы

**До 50 запросов** через `.call_batch()`:
```python
requests_data = {
    "deal1": client.crm.deal.get(bitrix_id=1),
    "deal2": client.crm.deal.get(bitrix_id=2),
}
batch = client.call_batch(requests_data)
for key, result in batch.result.result.items():
    print(f"{key}: {result}")
```

**Больше 50 запросов** через `.call_batches()`:
```python
requests = [
    client.crm.deal.get(bitrix_id=i)
    for i in range(1, 100)
]
batches = client.call_batches(requests)
for result in batches.result.result:
    print(result)
```

### 6. Обработка ошибок

Импортируйте исключения из `b24pysdk.error`:

```python
from b24pysdk.error import (
    BitrixAPIError,
    BitrixTimeout,
    BitrixAPIExpiredToken,
    BitrixRequestError
)

try:
    request = client.crm.deal.get(bitrix_id=999)
    deal = request.result
except BitrixTimeout as e:
    print(f"Таймаут: {e.timeout} сек")
except BitrixAPIExpiredToken:
    print("Токен истёк, SDK автоматически обновит токен")
except BitrixAPIError as e:
    print(f"Ошибка API: {e.error} - {e.error_description}")
except BitrixRequestError as e:
    print(f"Ошибка сети: {e.original_error}")
```

### 7. Конфигурация

Настройка таймаутов и retry через [`Config`](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/_config.py):

```python
from b24pysdk import Config

cfg = Config()
cfg.default_timeout = 30  # секунды или (connect, read) tuple
cfg.max_retries = 3
cfg.initial_retry_delay = 0.5
cfg.retry_delay_increment = 0.5
```

### 8. Соглашения об именовании методов

**Важно понимать:** SDK автоматически преобразует Python-методы в camelCase для Bitrix24 API.

| Python (snake_case) | Bitrix24 API (camelCase) |
|---------------------|--------------------------|
| `client.crm.deal.list()` | `crm.deal.list` |
| `client.crm.deal.get()` | `crm.deal.get` |
| `client.crm.deal.add()` | `crm.deal.add` |
| `client.crm.deal.update()` | `crm.deal.update` |
| `client.crm.deal.delete()` | `crm.deal.delete` |
| `client.crm.deal.fields()` | `crm.deal.fields` |
| `client.crm.deal.userfield.add()` | `crm.deal.userfield.add` |

**Преобразование реализовано в:** [`Base.__to_camel_case()`](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/base.py#L48-L51)

**Параметры методов:**
- `bitrix_id` — ID сущности в Bitrix24 (для `get`, `update`, `delete`)
- `fields` — словарь с полями сущности (для `add`, `update`)
- `select` — список полей для выборки (для `list`)
- `filter` — словарь с условиями фильтрации (для `list`)
- `order` — словарь с параметрами сортировки (для `list`)
- `start` — смещение для пагинации (для `list`)
- `timeout` — таймаут запроса в секундах

---

## Шаги при возникновении ошибок или сложностей

### 1. Определите тип ошибки

**Ошибка SDK** → Изучите исходный код SDK в GitHub
**Ошибка API Bitrix24** → Проверьте документацию REST API

### 2. Ключевые классы и файлы SDK

**Основные классы:**

| Компонент | Описание | Ссылка на GitHub |
|-----------|----------|------------------|
| **Client** | Главный класс для API-вызовов | [_client.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/_client.py) |
| **BitrixToken** | OAuth-аутентификация | [bitrix_token.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/bitrix_token.py) |
| **BitrixWebhook** | Webhook-аутентификация | [bitrix_token.py#L286](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/bitrix_token.py#L286) |
| **BitrixApp** | Данные приложения | [bitrix_app.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/bitrix_app.py) |
| **Config** | Настройки SDK | [_config.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/_config.py) |
| **Errors** | Все исключения SDK | [error.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/error.py) |

**Request классы:**

| Класс | Назначение | Ссылка |
|-------|-----------|--------|
| **BitrixAPIRequest** | Базовый класс запроса | [bitrix_api_request.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/classes/request/bitrix_api_request.py) |
| **BitrixAPIListRequest** | Запросы со списками (.as_list()) | [bitrix_api_list_request.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/classes/request/bitrix_api_list_request.py) |
| **BitrixAPIListFastRequest** | Быстрая пагинация (.as_list_fast()) | [bitrix_api_list_request.py#L73](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/classes/request/bitrix_api_list_request.py#L73) |
| **BitrixAPIBatchRequest** | Пакетные запросы | [bitrix_api_batch_request.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/classes/request/bitrix_api_batch_request.py) |

**Response классы:**

| Класс | Назначение | Ссылка |
|-------|-----------|--------|
| **BitrixAPIResponse** | Базовый класс ответа | [bitrix_api_response.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/classes/response/bitrix_api_response.py) |
| **BitrixAPIListResponse** | Ответы со списками | [bitrix_api_list_response.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/classes/response/bitrix_api_list_response.py) |
| **BitrixAPIBatchResponse** | Ответы пакетных запросов | [bitrix_api_batch_response.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/classes/response/bitrix_api_batch_response.py) |
| **BitrixAPITimeResponse** | Информация о времени выполнения | [bitrix_api_time_response.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/classes/response/bitrix_api_time_response.py) |

**Структура Response:**
```python
request = client.crm.deal.get(bitrix_id=123)

# request.result — результат API-вызова (данные)
# request.response — полный объект ответа
# request.response.result — результат API-вызова
# request.response.time — информация о времени выполнения
# request.response.time.duration — длительность в секундах
# request.response.total — общее количество записей (для list)
# request.response.next — следующее смещение для пагинации
```

### 3. Scope-специфичные классы

**CRM Scope:**

| Сущность | Класс | Ссылка |
|----------|-------|--------|
| **CRM (корневой)** | `CRM` | [crm/__init__.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/__init__.py) |
| **Deals** | `Deal` | [crm/deal/deal.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/deal/deal.py) |
| **Contacts** | `Contact` | [crm/contact.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/contact.py) |
| **Companies** | `Company` | [crm/company.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/company.py) |
| **Leads** | `Lead` | [crm/lead.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/lead.py) |
| **Quotes** | `Quote` | [crm/quote.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/quote.py) |
| **Activities** | `Activity` | [crm/activity/activity.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/activity/activity.py) |
| **Smart Processes** | `Item` | [crm/item/item.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/item/item.py) |
| **Timeline** | `Timeline` | [crm/timeline/timeline.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/timeline/timeline.py) |
| **Requisites** | `Requisite` | [crm/requisite/requisite.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/requisite/requisite.py) |

**Другие Scopes:**

| Scope | Класс | Ссылка |
|-------|-------|--------|
| **User** | `User` | [user/__init__.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/user/__init__.py) |
| **Department** | `Department` | [department.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/department.py) |
| **Socialnetwork** | `Socialnetwork` | [socialnetwork/__init__.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/socialnetwork/__init__.py) |

### 4. Функции низкого уровня

| Функция | Описание | Ссылка |
|---------|----------|--------|
| **call_method** | Одиночный API-вызов | [call_method.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/functions/call_method.py) |
| **call_batch** | Пакетный вызов (≤50) | [call_batch.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/functions/call_batch.py) |
| **call_batches** | Множественные батчи | [call_batches.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/functions/call_batches.py) |
| **call_list** | Пагинация списков | [call_list.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/functions/call_list.py) |
| **call_list_fast** | Быстрая пагинация | [call_list_fast.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/functions/call_list_fast.py) |

### 5. Исключения SDK

См. полный список в [error.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/error.py):

| Исключение | HTTP Code | Описание |
|------------|-----------|----------|
| `BitrixAPIExpiredToken` | 401 | Токен истёк |
| `BitrixAPINoAuthFound` | 401 | Неверные данные авторизации |
| `BitrixAPIAccessDenied` | 403 | REST API доступен только на коммерческих тарифах |
| `BitrixAPIInsufficientScope` | 403 | Недостаточно прав |
| `BitrixAPINotFound` | 404 | Ресурс не найден |
| `BitrixAPIQueryLimitExceeded` | 503 | Превышен лимит запросов |
| `BitrixTimeout` | 504 | Таймаут запроса |

### 6. Документация Bitrix24 REST API

**Официальная документация:**
- Главная: https://apidocs.bitrix24.com/
- GitHub (русская): https://github.com/bitrix-tools/b24-rest-docs
- OAuth 2.0: https://apidocs.bitrix24.com/api-reference/oauth/
- Вебхуки: https://apidocs.bitrix24.com/local-integrations/local-webhooks.html
- CRM: https://apidocs.bitrix24.com/api-reference/crm/
- Batch: https://apidocs.bitrix24.com/api-reference/how-to-call-rest-api/batch.html
- Пагинация: https://apidocs.bitrix24.com/api-reference/performance/huge-data.html

### 7. Алгоритм действий при ошибке

**Шаг 1:** Проверьте тип исключения
```python
try:
    result = client.crm.deal.get(bitrix_id=123).result
except Exception as e:
    print(type(e).__name__)  # Узнайте класс исключения
    print(e)
```

**Шаг 2:** Если `BitrixAPIError`, проверьте код ошибки
```python
except BitrixAPIError as e:
    print(f"Error code: {e.error}")
    print(f"Description: {e.error_description}")
    print(f"HTTP status: {e.status_code}")
```

**Шаг 3:** Найдите соответствующий класс исключения в [error.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/error.py)

**Шаг 4:** Проверьте документацию метода API в Bitrix24:
- Для `crm.deal.get` → https://apidocs.bitrix24.com/api-reference/crm/deals/crm-deal-get.html
- Для других методов замените URL на соответствующий

**Шаг 5:** Изучите исходный код метода в SDK:
- Например, для `client.crm.deal.get()` → [deal.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/deal/deal.py)

**Шаг 6:** Проверьте базовый класс:
- Все сущности CRM наследуют [`BaseItem`](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/item/base_item.py)
- Который наследует [`BaseCRM`](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/crm/base_crm.py)
- Который наследует [`Base`](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/scopes/base.py)

**Шаг 7:** Если проблема в низкоуровневом вызове, проверьте:
- [call_method.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/functions/call_method.py) — для обычных запросов
- [call.py](https://github.com/bitrix24/b24pysdk/blob/main/b24pysdk/bitrix_api/functions/call.py) — HTTP-запросы через `requests`

---

## Типичные проблемы и решения

### Проблема 1: "Метод не найден в SDK"

**Причина:** Не все методы Bitrix24 REST API реализованы в SDK.

**Решение:** Используйте низкоуровневый вызов через `call_method`:
```python
# Если метод отсутствует в SDK
response = client._bitrix_token.call_method(
    api_method="crm.custom.method",
    params={"id": 123}
)
print(response["result"])
```

### Проблема 2: "Превышен лимит запросов (QUERY_LIMIT_EXCEEDED)"

**Причина:** Bitrix24 ограничивает количество запросов в секунду (обычно 2 запроса/сек для большинства тарифов).

**Решение:** 
1. Используйте пакетные запросы `.call_batch()` или `.call_batches()`
2. Добавьте задержки между запросами
3. Проверьте лимиты вашего тарифа: https://apidocs.bitrix24.com/api-reference/limits.html

### Проблема 3: "Токен истёк, но не обновляется автоматически"

**Причина:** Автообновление работает только для OAuth-токенов с `refresh_token`.

**Решение:**
```python
# Для вебхуков автообновление невозможно
# Для OAuth убедитесь, что передан refresh_token:
bitrix_token = BitrixToken(
    domain="example.bitrix24.com",
    auth_token="access_token",
    refresh_token="refresh_token",  # Обязательно!
    bitrix_app=bitrix_app
)

# Отключить автообновление:
bitrix_token.AUTO_REFRESH = False
```

### Проблема 4: "Неверный формат вебхука"

**Причина:** Неправильный формат `auth_token` для `BitrixWebhook`.

**Решение:**
```python
# Правильный формат: "user_id/webhook_key"
# Для URL: https://example.bitrix24.com/rest/1/abc123def456/crm.deal.list

bitrix_token = BitrixWebhook(
    domain="example.bitrix24.com",  # БЕЗ https:// и /rest/
    auth_token="1/abc123def456"     # user_id/webhook_key
)
```

### Проблема 5: "Ошибка при работе со списками (пагинация)"

**Причина:** Неправильное использование методов `.as_list()` и `.as_list_fast()`.

**Решение:**
```python
# ✅ Правильно - вызов .result после .as_list()
all_deals = client.crm.deal.list().as_list().result

# ❌ Неправильно - забыли .result
all_deals = client.crm.deal.list().as_list()  # Это объект запроса, а не данные

# ✅ Для больших датасетов используйте .as_list_fast()
for deal in client.crm.deal.list().as_list_fast().result:
    print(deal["TITLE"])
```

---

## Полезные ссылки

- **Репозиторий SDK:** https://github.com/bitrix24/b24pysdk
- **PyPI:** https://pypi.org/project/b24pysdk/
- **Issues:** https://github.com/bitrix24/b24pysdk/issues
- **REST API Docs:** https://apidocs.bitrix24.com/
- **REST API GitHub:** https://github.com/bitrix-tools/b24-rest-docs
- **README SDK:** [README.md](https://github.com/bitrix24/b24pysdk/blob/main/README.md)
- **Лимиты API:** https://apidocs.bitrix24.com/api-reference/limits.html

---

## Примеры типичных сценариев

### Пример 1: Создание сделки
```python
from b24pysdk import BitrixWebhook, Client

bitrix_token = BitrixWebhook(
    domain="example.bitrix24.com",
    auth_token="1/abc123"
)
client = Client(bitrix_token)

deal_data = {
    "TITLE": "Новая сделка",
    "STAGE_ID": "NEW",
    "OPPORTUNITY": 50000,
    "CURRENCY_ID": "RUB"
}

request = client.crm.deal.add(fields=deal_data)
deal_id = request.result
print(f"Создана сделка ID: {deal_id}")
```

### Пример 2: Массовое обновление
```python
deals_to_update = [1, 2, 3, 4, 5]

requests = [
    client.crm.deal.update(
        bitrix_id=deal_id,
        fields={"STAGE_ID": "WON"}
    )
    for deal_id in deals_to_update
]

batch = client.call_batches(requests)
print(f"Обновлено {len(batch.result.result)} сделок")
```

### Пример 3: Обработка ошибок
```python
from b24pysdk.error import (
    BitrixAPIError,
    BitrixAPIExpiredToken,
    BitrixAPINotFound
)

try:
    request = client.crm.deal.get(bitrix_id=99999)
    deal = request.result
except BitrixAPINotFound:
    print("Сделка не найдена")
except BitrixAPIExpiredToken:
    print("Токен истёк, будет обновлён автоматически")
except BitrixAPIError as e:
    print(f"Ошибка API: {e.error}")
```

---

**Версия SDK:** 0.1.0a1  
**Python:** 3.9+  
**Лицензия:** MIT

