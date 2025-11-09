# 🧱 DDD User Registration Example (Laravel 12)

Этот мини‑проект демонстрирует реализацию регистрации пользователя в архитектуре **Domain‑Driven Design (DDD)** с использованием Laravel 12.

## 🧩 Основная идея

Проект разделён на слои, каждый из которых выполняет строго определённую роль:

```
app/
 ├── Domain/        # Чистая бизнес‑логика (сущности, value objects, интерфейсы репозиториев)
 ├── Application/   # Сценарии использования, команды, обработчики, события и слушатели
 ├── Infrastructure/# Реализация репозиториев, интеграция с Laravel, хранение данных
 ├── UI/            # Входные точки (HTTP, CLI и т.п.)
```

---

## ⚙️ Структура проекта

### Domain

- `Shared/Exceptions/BusinessException.php` — базовый класс для всех исключений бизнес-логики. 
- `Shared/ValueObjects/Uuid.php` — общий объект-значение UUID через пакет Ramsey\Uuid. 
- `User/Entities/User.php` — бизнес‑модель пользователя без зависимостей от фреймворка.  
- `User/ValueObjects/Email.php`, `Password.php` — объект‑значения, отвечающие за валидацию и инкапсуляцию данных.  
- `User/Repositories/UserRepositoryInterface.php` — интерфейс для хранилища пользователей.
- `User/Services/RegisterUserService.php` — сервис регистрации пользователя.
- `User/Exceptions/InvalidEmail.php`, `InvalidPassword.php`, `UserAlreadyExists.php` — специализированные ошибки бизнес-логики.

### Application

- `User/Commands/RegisterUserCommand.php` — входные данные для регистрации.  
- `User/Handlers/RegisterUserHandler.php` — прикладная логика регистрации.  
- `User/Events/UserRegistered.php` — событие о создании нового пользователя.  
- `User/Listeners/SendWelcomeEmail.php` — пример слушателя события.

### Infrastructure

- `User/Persistence/Eloquent/Models/EloquentUser.php` — модель пользователя через Eloquent. 
- `User/Persistence/Eloquent/Repositories/EloquentUserRepository.php` — реализация репозитория через Eloquent.  

### UI

- `API/Controllers/RegisterApiController.php` - REST‑точка для регистрации пользователя.
- `CLI/Commands/RegisterUserCommand.php` — консольная команда `user:register`.
- `WEB/Controllers/RegisterController.php` — веб-контроллер регистрации пользователя.  
- `WEB/Views/` — Blade-шаблоны Laravel для отображения формы регистрации.  

### Laravel

- `app/Providers/AppServiceProvider.php` — биндинг интерфейсов и подписка на события.
- `src/bootstrap/app.php` — настройка приложения (роутеры, события, перехват ошибок, команды).
- `src/database/migrations/2025_11_09_120435_change_id_to_uuid_in_users_and_sessions_tables.php`, `2025_11_09_121335_make_user_name_nullable.php` — изменение таблицы User на использование UUID в качестве ID.
- `src/routes/api.php` — роутер для регистрации пользователя через API.
- `src/routes/web.php` — роутеры для регистрации пользователя через WEB-интерфейс.

---

## 🚀 Пример регистрации пользователя

### HTTP (WEB)

```http
http://localhost/register
```

### HTTP (API)

```http
curl -X POST http://localhost/api/register 
-H "Content-Type: application/json"      
-H "Accept: application/json"      
-d '{"email":"test@ddd.com", "password":"1234567"}'
```

**Response (201):**
```json
{
  "status": "ok",
  "data": {
    "id": 1,
    "email": "test@example.com"
  }
}
```

**Response (400):**
```json
{
  "error": "Email already exists",
}
```

### CLI

```bash
php artisan user:register test@example.com secret123
```

---

## ⚡ Настройка Laravel Application Builder

Регистрация слоёв выполнена в `bootstrap/app.php`:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
)
->withCommands([
    __DIR__.'/../app/UI/CLI/Commands',
])
->withEvents(discover: [
    __DIR__.'/../app/Application/*/Listeners',
])
```

Большой блок кода для регистрации и отображения ошибок в зависимости от контекста (web/api/debug):

```php
->withExceptions(function (Exceptions $exceptions): void {

    $exceptions->render(function (Throwable $e, $request) {
        // 1️⃣ ValidationException — стандартная обработка Laravel
        if ($e instanceof ValidationException) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        // 2️⃣ BusinessException — безопасно показываем пользователю
        if ($e instanceof BusinessException) {
            if (method_exists($request, 'wantsJson') && $request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }

        if (env('APP_DEBUG', false)) {
            // Dev-режим: показываем полное сообщение и stack trace
            if (method_exists($request, 'wantsJson') && $request->wantsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                ], 500);
            }

            // Для WEB
            return response(
                "<h1>Ошибка разработки</h1><pre>{$e}</pre>",
                500
            );
        }

        // 3️⃣ Любые другие исключения — внутренняя ошибка
        if (method_exists($request, 'wantsJson') && $request->wantsJson()) {
            return response()->json(['error' => 'Что-то пошло не так. Попробуйте позже.'], 500);
        }

        return response('Произошла непредвиденная ошибка', 500);
    });
})
```

---

## 🧠 Принципы, используемые в проекте

- **Separation of Concerns (SoC)** — каждый слой решает только свою задачу.  
- **Dependency Inversion** — зависимости направлены изнутри наружу (Domain не знает о Laravel).  
- **CQRS‑подход** — разделение команд и запросов.  
- **Событийная архитектура** — Application реагирует на доменные события.

---

## 🧩 Возможное развитие

- Добавить подтверждение email через события.  
- Подключить EventStore / очередь (например, Redis).  
- Написать тесты для Domain и Application слоёв без Laravel.

---

## 🧰 Требования

- PHP >= 8.3  
- Laravel 12  
- SQLite/MySQL/PostgreSQL

---

## 📦 Установка и запуск

```bash
git clone https://github.com/vanyazaov/ddd-user-registration.git
cd ddd-user-registration/docker

docker compose up -d --build

# Создайте .env файл для настройки проекта
docker compose exec -T app bash -c "cd /var/www/html && cp .env.example .env"

# Сгенерируйте ключ приложения
docker compose exec -T app bash -c "cd /var/www/html && php artisan key:generate --force"

# Измените настройки в созданном .env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=advertising_pg
DB_USERNAME=postgres
DB_PASSWORD=root

CACHE_DRIVER=redis
QUEUE_CONNECTION=rabbitmq

REDIS_HOST=redis
REDIS_PORT=6379

RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest

# Установка зависимостей
docker compose exec app composer install
docker compose exec app php artisan migrate
```

---

## 🧑‍💻 Автор

**Ivan**  
Разработчик, исследующий архитектурные подходы и чистый код.

---

> Проект создан как демонстрация архитектурных принципов DDD, а не как готовое production‑решение.
