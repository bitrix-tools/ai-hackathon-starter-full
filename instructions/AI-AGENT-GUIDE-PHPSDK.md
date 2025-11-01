# Инструкция для ИИ агентов: Bitrix24 PHP SDK

## 📋 Краткая сводка по SDK

**Bitrix24 PHP SDK** — официальная PHP библиотека для работы с REST API Bitrix24.

### Основные характеристики:
- **Версия**: 1.7.* (стабильная)
- **Требования**: PHP 8.2+, ext-json, ext-curl, ext-intl
- **Лицензия**: MIT
- **Репозиторий**: https://github.com/bitrix24/b24phpsdk

### Ключевые возможности:
- ✅ Поддержка OAuth 2.0 для приложений
- ✅ Поддержка входящих вебхуков для простых интеграций
- ✅ Автоматическое обновление access токенов
- ✅ Batch-запросы с генераторами PHP для эффективной работы с большими данными
- ✅ Типизированные методы и результаты для автодополнения в IDE
- ✅ Обработка событий Bitrix24 (webhook-уведомления)

---

## 🚀 Установка

### Composer установка:
```bash
composer require bitrix24/b24phpsdk
```

### Для Windows пользователей:
- Используйте WSL (Windows Subsystem for Linux)
- Отключите флаг `git config --global core.protectNTFS false` для работы с файлами, начинающимися с точки

### Зависимости в composer.json:
```json
{
  "require": {
    "bitrix24/b24phpsdk": "1.7.*"
  }
}
```

**📚 Документация по установке**: [README.md](https://github.com/bitrix24/b24phpsdk/blob/main/README.md#installation)

---

## 🏗️ Основные требования к разработке приложений

### 1. Инициализация SDK

#### Работа с вебхуком (простые интеграции):
```php
use Bitrix24\SDK\Services\ServiceBuilderFactory;

$serviceBuilder = ServiceBuilderFactory::createServiceBuilderFromWebhook(
    'https://your-portal.bitrix24.com/rest/1/webhook_code/'
);
```

#### Работа с OAuth приложением (marketplace):
```php
use Bitrix24\SDK\Services\ServiceBuilderFactory;
use Bitrix24\SDK\Core\Credentials\ApplicationProfile;
use Symfony\Component\HttpFoundation\Request;

$appProfile = ApplicationProfile::initFromArray([
    'BITRIX24_PHP_SDK_APPLICATION_CLIENT_ID' => 'your_client_id',
    'BITRIX24_PHP_SDK_APPLICATION_CLIENT_SECRET' => 'your_client_secret',
    'BITRIX24_PHP_SDK_APPLICATION_SCOPE' => 'crm,user,task'
]);

$serviceBuilder = ServiceBuilderFactory::createServiceBuilderFromPlacementRequest(
    Request::createFromGlobals(),
    $appProfile
);
```

**📖 Подробнее**: [docs/EN/README.md](https://github.com/bitrix24/b24phpsdk/blob/main/docs/EN/README.md#authorisation)

### 2. Работа с сервисами

SDK организован по **scope** (областям доступа) Bitrix24 API. Каждый scope имеет свой ServiceBuilder:

```php
// CRM операции
$crmService = $serviceBuilder->getCRMScope();
$contact = $crmService->contact()->add(['NAME' => 'Иван', 'LAST_NAME' => 'Иванов']);

// Работа с задачами
$taskService = $serviceBuilder->getTaskScope();
$tasks = $taskService->task()->list();

// Пользователи
$userService = $serviceBuilder->getUserScope();
$currentUser = $userService->user()->current();
```

**Доступные scope**:
- `getCRMScope()` - CRM
- `getTaskScope()` - Задачи
- `getUserScope()` - Пользователи
- `getDiskScope()` - Диск (файлы)
- `getCalendarScope()` - Календарь
- `getTelephonyScope()` - Телефония
- `getSaleScope()` - Продажи/заказы
- `getMainScope()` - Основные методы
- `getEntityScope()` - Универсальное хранилище
- `getBizProcScope()` - Бизнес-процессы
- И другие...

**📚 Полный список scope**: [src/Services/ServiceBuilder.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/ServiceBuilder.php)

### 3. Вызов неподдерживаемых методов

Если метод еще не реализован в SDK, используйте прямой вызов через Core:

```php
$result = $serviceBuilder->core->call('user.current');
$data = $result->getResponseData()->getResult();
```

**⚠️ Важно**: После использования создайте [Feature Request](https://github.com/bitrix24/b24phpsdk/issues/new?assignees=&labels=enhancement+in+SDK&projects=&template=2_feature_request_sdk.yaml)

### 4. Обработка ошибок

SDK использует иерархию исключений в `Bitrix24\SDK\Core\Exceptions\`:

```php
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Core\Exceptions\AuthForbiddenException;

try {
    $result = $serviceBuilder->core->call('some.method');
} catch (AuthForbiddenException $e) {
    // Проблемы с авторизацией
} catch (InvalidArgumentException $e) {
    // Неверные аргументы вызова
} catch (TransportException $e) {
    // Сетевые ошибки
} catch (\Throwable $e) {
    // Все остальные ошибки
}
```

**📖 Подробнее об исключениях**: [src/Core/Exceptions/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Core/Exceptions)

---

## 🔍 Решение проблем и отладка

### Шаг 1: Проверьте официальную документацию SDK

Если возникла ошибка, первым делом проверьте:

1. **README проекта**: [README.md](https://github.com/bitrix24/b24phpsdk/blob/main/README.md)
2. **Внутреннюю документацию**: [docs/EN/README.md](https://github.com/bitrix24/b24phpsdk/blob/main/docs/EN/README.md)
3. **AI-README с архитектурой**: [AI-README.md](https://github.com/bitrix24/b24phpsdk/blob/main/AI-README.md)

### Шаг 2: Изучите реализацию метода

Каждый метод в SDK имеет атрибут `ApiEndpointMetadata` со ссылкой на документацию:

```php
#[ApiEndpointMetadata(
    'crm.contact.add',
    'https://apidocs.bitrix24.com/api-reference/crm/contacts/crm-contact-add.html',
    'Creates new contact'
)]
public function add(array $fields): AddedItemResult
```

**Действия**:
1. Найдите нужный метод в соответствующем сервисе в `src/Services/`
2. Посмотрите на атрибут `ApiEndpointMetadata` - там есть ссылка на официальную документацию
3. Проверьте сигнатуру метода и типы параметров

**Примеры путей к сервисам**:
- CRM контакты: [src/Services/CRM/Contact/Service/Contact.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/CRM/Contact/Service/Contact.php)
- Задачи: [src/Services/Task/Service/Task.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/Task/Service/Task.php)
- Пользователи: [src/Services/User/Service/User.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/User/Service/User.php)

### Шаг 3: Проверьте результаты (Result классы)

Результаты методов возвращают типизированные объекты. Проверьте:

1. **Result классы** в папке `Result/` рядом с сервисом
2. **Свойства через PHPDoc** - они описывают доступные поля
3. **Методы обработки** результата

Пример:
```php
// src/Services/CRM/Contact/Result/ContactItemResult.php
/**
 * @property-read int $ID
 * @property-read string $NAME
 * @property-read string $LAST_NAME
 * @property-read CarbonImmutable $DATE_CREATE
 */
class ContactItemResult extends AbstractCrmItem
```

### Шаг 4: Изучите примеры

SDK содержит рабочие примеры:

- **Примеры с вебхуком**: [examples/webhook/](https://github.com/bitrix24/b24phpsdk/tree/main/examples/webhook)
- **Примеры локального приложения**: [examples/local-app/](https://github.com/bitrix24/b24phpsdk/tree/main/examples/local-app)
- **Примеры с Workflows**: [examples/local-app-workflows/](https://github.com/bitrix24/b24phpsdk/tree/main/examples/local-app-workflows)

### Шаг 5: Проверьте интеграционные тесты

Интеграционные тесты показывают реальные сценарии использования:

- **Core тесты**: [tests/Integration/Core/](https://github.com/bitrix24/b24phpsdk/tree/main/tests/Integration/Core)
- **Тесты по scope**: [tests/Integration/Services/](https://github.com/bitrix24/b24phpsdk/tree/main/tests/Integration/Services)

**Полезные примеры**:
- CRM: `tests/Integration/Services/CRM/`
- Tasks: `tests/Integration/Services/Task/`
- Users: `tests/Integration/Services/User/`

### Шаг 6: Обратитесь к REST API документации Bitrix24

Официальная документация REST API Bitrix24:
- **🌐 Основная документация**: https://apidocs.bitrix24.com/
- **📖 GitHub документация**: https://github.com/bitrix-tools/b24-rest-docs

**Структура REST API**:
- Методы группируются по областям (scope): crm, task, user, disk и т.д.
- Каждый метод имеет описание параметров, результатов и примеров
- SDK методы точно соответствуют REST API методам

### Шаг 7: Проверьте требования scope

Многие ошибки связаны с недостаточными правами (scope). Проверьте:

1. **Список scope** в `ApplicationProfile` при OAuth авторизации
2. **Права вебхука** (должны быть установлены все необходимые галочки)
3. **Доступные scope** в [src/Core/Credentials/Scope.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Core/Credentials/Scope.php)

```php
// Проверьте, что у приложения есть нужные scope:
'BITRIX24_PHP_SDK_APPLICATION_SCOPE' => 'crm,user,task,disk'
```

---

## 📦 Архитектура и ключевые классы

### Уровни абстракции:

```
HTTP/2 + JSON
    ↓
Symfony HTTP Client
    ↓
Core\ApiClient (работа с REST API endpoints)
    ↓
Services\* (работа с сущностями Bitrix24)
```

### Ключевые интерфейсы:

1. **CoreInterface** - [src/Core/Contracts/CoreInterface.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Core/Contracts/CoreInterface.php)
   - Основной интерфейс для вызова API методов
   - Метод `call(string $apiMethod, array $parameters = []): Response`

2. **BatchOperationsInterface** - [src/Core/Contracts/BatchOperationsInterface.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Core/Contracts/BatchOperationsInterface.php)
   - Интерфейс для batch-операций
   - Эффективная работа с большими объемами данных

3. **ServiceBuilder** - [src/Services/ServiceBuilder.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/ServiceBuilder.php)
   - Главная точка входа для доступа к сервисам
   - Методы вида `getCRMScope()`, `getTaskScope()` и т.д.

4. **ServiceBuilderFactory** - [src/Services/ServiceBuilderFactory.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/ServiceBuilderFactory.php)
   - Фабрика для создания ServiceBuilder
   - Статические методы:
     - `createServiceBuilderFromWebhook(string $webhookUrl)`
     - `createServiceBuilderFromPlacementRequest(Request $request, ApplicationProfile $profile)`

### Базовые классы для сервисов:

- **AbstractService** - [src/Services/AbstractService.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/AbstractService.php)
  - Базовый класс для всех сервисов

- **AbstractBatchService** - [src/Services/AbstractBatchService.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/AbstractBatchService.php)
  - Базовый класс для batch-операций

- **AbstractServiceBuilder** - [src/Services/AbstractServiceBuilder.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/AbstractServiceBuilder.php)
  - Базовый класс для построителей сервисов

### Результаты (Results):

Все результаты наследуются от [src/Core/Result/AbstractResult.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Core/Result/AbstractResult.php):

- **AddedItemResult** - результат добавления элемента
- **UpdatedItemResult** - результат обновления элемента
- **DeletedItemResult** - результат удаления элемента
- **FieldsResult** - результат получения полей

---

## 🎯 Разработка новых функций в SDK

### Если вам нужно добавить поддержку нового метода API:

1. **Определите scope** метода (crm, task, user и т.д.)
2. **Найдите соответствующий ServiceBuilder** в `src/Services/`
3. **Изучите структуру** похожих методов в том же сервисе
4. **Проверьте документацию** на https://apidocs.bitrix24.com/
5. **Создайте Issue** с типом [Feature Request](https://github.com/bitrix24/b24phpsdk/issues/new?assignees=&labels=enhancement+in+SDK&projects=&template=2_feature_request_sdk.yaml)

### Contributing в SDK:

Если хотите внести изменения:

1. **Форкните репозиторий**
2. **Изучите**: [CONTRIBUTING.md](https://github.com/bitrix24/b24phpsdk/blob/main/CONTRIBUTING.md)
3. **Изучите архитектуру**: [AI-README.md](https://github.com/bitrix24/b24phpsdk/blob/main/AI-README.md)
4. **Следуйте стандартам кодирования**:
   - PSR-12
   - PHPStan level 9
   - Типизация всех параметров и возвращаемых значений
5. **Создайте Pull Request** в ветку `dev` (не в `main`!)

**📖 Руководство по контрибуции**: [CONTRIBUTING.md](https://github.com/bitrix24/b24phpsdk/blob/main/CONTRIBUTING.md)

**🏗️ Архитектурный гайд**: [AI-README.md](https://github.com/bitrix24/b24phpsdk/blob/main/AI-README.md)

---

## 🔗 Полезные ссылки для отладки

### Документация SDK:
- 📘 [README.md](https://github.com/bitrix24/b24phpsdk/blob/main/README.md) - Основная документация
- 🏗️ [AI-README.md](https://github.com/bitrix24/b24phpsdk/blob/main/AI-README.md) - Архитектурный обзор
- 📚 [docs/EN/README.md](https://github.com/bitrix24/b24phpsdk/blob/main/docs/EN/README.md) - Внутренняя документация
- 🤝 [CONTRIBUTING.md](https://github.com/bitrix24/b24phpsdk/blob/main/CONTRIBUTING.md) - Гайд для контрибьюторов
- 🔄 [CHANGELOG.md](https://github.com/bitrix24/b24phpsdk/blob/main/CHANGELOG.md) - История изменений

### Основные классы и контракты:
- 🎯 [src/Core/Contracts/CoreInterface.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Core/Contracts/CoreInterface.php)
- 🏭 [src/Services/ServiceBuilderFactory.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/ServiceBuilderFactory.php)
- 🔧 [src/Services/ServiceBuilder.php](https://github.com/bitrix24/b24phpsdk/blob/main/src/Services/ServiceBuilder.php)
- 🔑 [src/Core/Credentials/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Core/Credentials) - Работа с авторизацией
- ⚠️ [src/Core/Exceptions/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Core/Exceptions) - Исключения

### Реализации сервисов:
- 💼 CRM: [src/Services/CRM/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Services/CRM)
- ✅ Tasks: [src/Services/Task/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Services/Task)
- 👤 Users: [src/Services/User/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Services/User)
- 💾 Disk: [src/Services/Disk/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Services/Disk)
- 📅 Calendar: [src/Services/Calendar/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Services/Calendar)
- ☎️ Telephony: [src/Services/Telephony/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Services/Telephony)
- 🛒 Sale: [src/Services/Sale/](https://github.com/bitrix24/b24phpsdk/tree/main/src/Services/Sale)

### Примеры:
- 📝 [examples/webhook/](https://github.com/bitrix24/b24phpsdk/tree/main/examples/webhook) - Примеры с вебхуком
- 🔌 [examples/local-app/](https://github.com/bitrix24/b24phpsdk/tree/main/examples/local-app) - Локальное приложение
- ⚙️ [examples/local-app-workflows/](https://github.com/bitrix24/b24phpsdk/tree/main/examples/local-app-workflows) - С бизнес-процессами

### Тесты:
- 🧪 [tests/Integration/](https://github.com/bitrix24/b24phpsdk/tree/main/tests/Integration) - Интеграционные тесты
- 🔬 [tests/Unit/](https://github.com/bitrix24/b24phpsdk/tree/main/tests/Unit) - Юнит-тесты

### Внешняя документация:
- 🌐 **Bitrix24 REST API**: https://apidocs.bitrix24.com/
- 📖 **GitHub REST API Docs**: https://github.com/bitrix-tools/b24-rest-docs
- 🐙 **SDK репозиторий**: https://github.com/bitrix24/b24phpsdk
- 🐛 **Issues**: https://github.com/bitrix24/b24phpsdk/issues
- ✨ **Feature Requests**: https://github.com/bitrix24/b24phpsdk/issues/new?assignees=&labels=enhancement+in+SDK&projects=&template=2_feature_request_sdk.yaml

---

## ⚡ Быстрая справка по командам

### Makefile команды для разработки:

```bash
# Статический анализ
make lint-phpstan          # PHPStan проверка
make lint-rector           # Rector проверка
make lint-rector-fix       # Rector автоисправление
make lint-cs-fixer         # PHP CS Fixer

# Тестирование
make test-unit                     # Юнит-тесты
make test-integration-core         # Интеграционные тесты Core
make test-integration-scope-crm    # Интеграционные тесты CRM
make test-integration-scope-task   # Интеграционные тесты Task

# Документация
make build-documentation   # Обновить список методов в документации
```

**📖 Подробнее**: [Makefile](https://github.com/bitrix24/b24phpsdk/blob/main/Makefile)

---

## 💡 Советы для ИИ агентов

### При возникновении ошибки:

1. **Прочитайте сообщение об исключении** - оно содержит подробную информацию
2. **Проверьте namespace исключения** - он указывает на категорию проблемы
3. **Найдите соответствующий класс в SDK** - используйте поиск по коду
4. **Изучите документацию метода** через атрибут `ApiEndpointMetadata`
5. **Проверьте официальную REST API документацию** Bitrix24
6. **Посмотрите примеры использования** в тестах или examples

### При работе с новым scope:

1. **Проверьте существование ServiceBuilder** для этого scope
2. **Изучите структуру методов** в похожих сервисах (например, CRM)
3. **Используйте прямой вызов через Core**, если метод не реализован
4. **Создайте Feature Request**, чтобы метод добавили в SDK

### При разработке приложения:

1. **Используйте типизацию** - SDK полностью типизирован
2. **Обрабатывайте исключения** - не полагайтесь на успешное выполнение
3. **Используйте batch-операции** для больших объемов данных
4. **Логируйте операции** - передавайте PSR-3 Logger в ServiceBuilderFactory
5. **Следуйте 12-factor app** принципам для конфигурации

---

**Версия документа**: 1.0 (для SDK v1.7.*)
**Дата последнего обновления**: 2025-10-23
**Целевая аудитория**: ИИ агенты, работающие с Bitrix24 PHP SDK

---

## 🆘 Экстренная помощь

Если ничего не помогает:

1. 🔍 Проверьте [открытые Issues](https://github.com/bitrix24/b24phpsdk/issues) - возможно, проблема уже известна
2. 🐛 Создайте [Bug Report](https://github.com/bitrix24/b24phpsdk/issues/new) с подробным описанием
3. 💬 Обратитесь к [официальной документации Bitrix24](https://apidocs.bitrix24.com/)
4. 📧 Свяжитесь с поддержкой через GitHub Issues

**Помните**: SDK - это обертка над REST API, поэтому всегда сверяйтесь с официальной документацией Bitrix24 REST API!

