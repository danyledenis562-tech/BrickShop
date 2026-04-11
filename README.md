# Brick Shop

Це мій навчальний проєкт інтернет‑магазину LEGO на Laravel.

## Що потрібно
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL або PostgreSQL

## Як запустити локально

### Перший раз (ініціалізація)
1. Скопіювати `.env.example` у `.env` та вказати доступи до БД.
2. Встановити залежності: `composer install`, `npm install`.
3. **`php artisan key:generate`** — **лише якщо** в `.env` ще немає `APP_KEY=` (рядок порожній). Якщо ключ уже є, **не** запускай команду знову: новий ключ інвалідує зашифровані сесії та куки, здаватиметься, що «паролі не працюють», хоча в БД вони не змінюються.
4. `php artisan storage:link` (один раз, якщо немає `public/storage`).
5. `php artisan migrate` (або `migrate --seed` один раз, якщо потрібні демо-дані).
6. Фронт: `npm run dev` або `npm run build`.

**Не** використовуй для щоденного старту `php artisan migrate:fresh` чи `migrate:refresh` — вони **повністю очищають** таблиці і втрачаються всі користувачі та замовлення.

### Щоденний запуск
Достатньо: MySQL увімкнено, `php artisan serve` (або віртуальний хост XAMPP), `npm run dev`. Команди `migrate`, `key:generate` і `db:seed` **не** потрібні щоразу.

### Скрипт `start.sh` (наприклад, на сервері)
Він виконує `php artisan migrate` (це **не** стирає дані, лише застосовує нові міграції). Якщо встановити змінну **`SEED_ON_START=true`**, після старту виконається `db:seed`. Сидер більше **не** скидає паролі вже існуючих демо-користувачів при повторних запусках (лише створює відсутні записи).

### Якщо на проді не відображаються фото товарів
Причина часто в блокуванні зовнішніх CDN/джерел. Є команда, яка завантажує всі зовнішні URL фото у локальне сховище і переписує шляхи в БД:

- `php artisan shop:mirror-product-images`
- `php artisan shop:mirror-product-images --force` (перекачати знову)

Для Railway/Render можна запустити це автоматично під час деплою через `start.sh`:
- `MIRROR_IMAGES_ON_START=true` — один раз прогнати міграцію фото на сервері;
- `MIRROR_IMAGES_FORCE=true` — примусово перекачати всі фото (опційно).

Після успішного запуску поверни `MIRROR_IMAGES_ON_START=false`, щоб команда не запускалась на кожен деплой.

### Варіант через Cloudinary (рекомендовано для продакшн)
Команда завантажує фото товарів у Cloudinary і оновлює `product_images.path` на `secure_url`:

- `php artisan shop:sync-product-images-to-cloudinary`
- `php artisan shop:sync-product-images-to-cloudinary --force`

Потрібні змінні:
- `CLOUDINARY_CLOUD_NAME`
- `CLOUDINARY_API_KEY`
- `CLOUDINARY_API_SECRET`
- `CLOUDINARY_FOLDER=brickshop/products` (опційно)

Автозапуск у `start.sh`:
- `CLOUDINARY_SYNC_ON_START=true`
- `CLOUDINARY_SYNC_FORCE=true` (опційно)

## Деплой (production)
Після `composer install --no-dev` та міграцій виконай кеш для прискорення:
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

## Коротко про функціонал
- каталог товарів, пошук, фільтри
- сторінка товару з відгуками
- кошик і оформлення замовлення
- профіль користувача
- адмін‑панель
- кілька мов
