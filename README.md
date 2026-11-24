# Каталог настільних ігор

Веб-сервіс на Symfony: каталог настільних ігор із категоріями.
Сутності: `Category` (1) → `BoardGame` (N).

## Можливості
- Сайт на Twig: список ігор із фільтром (категорія, ціна, кількість гравців, пошук за назвою), сторінка гри `/game/{id}`, сторінка категорії `/category/{id}`
- JSON API: `/api/games`, `/api/games/{id}`, `/api/categories`
- Адмінка EasyAdmin (`/admin`) з CRUD для категорій та ігор, доступ лише для ROLE_ADMIN
- Валідація полів, фільтр через QueryBuilder
- Консольний клієнт на Python (`client/`), у тому числі вхід в адмінку через сесію

## Вимоги
PHP 8.2+ (розширення `pdo_sqlite`, `intl`, `mbstring`), Composer, Symfony CLI, Python 3.10+

## Запуск
```bash
composer install
```

Створіть файл `.env.local` з рядком:
```
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

Далі:
```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console app:create-admin admin@example.com
symfony serve
```

Команда `app:create-admin` запитає пароль у консолі (у коді його немає, у базі зберігається лише хеш).

Сайт: http://127.0.0.1:8000, адмінка: http://127.0.0.1:8000/admin

## JSON API
- `GET /api/games?category=1&maxPrice=1500&players=4&q=cat`
- `GET /api/games/{id}`
- `GET /api/categories`

## Python-клієнт
```bash
cd client
python -m venv .venv
.venv\Scripts\activate        # Linux/macOS: source .venv/bin/activate
pip install -r requirements.txt
python client.py categories
python client.py list --max-price 1500 --players 4
python client.py show 1
python client.py admin-check  # вхід в адмінку через сесію
```

Адреса сервера задається змінною `API_URL` (за замовчуванням http://127.0.0.1:8000).
Для `admin-check` можна задати `ADMIN_EMAIL`, пароль запитується в консолі.

## Примітки
- Видалити категорію, у якій є ігри, не вдасться (обмеження зовнішнього ключа).
-БД - SQLite; таблиці створюються лише міграціями.