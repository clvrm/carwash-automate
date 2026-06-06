# Carwash

CRM для автомойки: журнал записей, заказы, персонал, прайс-лист, аналитика. Есть REST API для мобильного приложения.

Стек: PHP 7.4, Yii2, MySQL 8.

## Запуск через Docker

```bash
docker compose up -d
docker compose exec php composer install
docker compose exec php php yii utility/init-rbac
```

Сайт: http://localhost:8000

Демо-аккаунт:
- email: `demo@carwash.local`
- пароль: `demo123`

Письма (регистрация, сброс пароля) сохраняются в `runtime/mail/` — SMTP не настроен.

## Локальная установка без Docker

1. `composer install`
2. Создать БД `carwash`, импортировать `docker/mysql/init/01-schema.sql` и `02-seed.sql`
3. Скопировать `config/db-local.php.example` → `config/db-local.php`, прописать доступ к БД
4. `php yii utility/init-rbac`
5. Настроить веб-сервер на каталог `web/`

## Конфигурация

- `config/db.php` — подключение к БД (дефолты для Docker)
- `config/params.php` — основные параметры
- `config/*-local.php` — локальные переопределения (не в git)
- `.env.example` — переменные окружения для Docker

Опционально:
- DaData — ключи в `config/params-local.php`, без них определение города по IP не работает
- Firebase — положить `credentials.json` в `commons/notification/firebase/`

## Структура

```
controllers/   веб-контроллеры
models/        ActiveRecord-модели
modules/api/   REST API v1
modules/admin/ админ-панель
views/         шаблоны
commons/       хелперы, RBAC, уведомления
commands/      консольные команды (yii)
```

## Консоль

```bash
php yii utility/init-rbac   # роли и права
```
