# Brick Shop

Вітрина та оформлення замовлень для LEGO-наборів: Laravel (бекенд + Blade), Vite, MySQL або PostgreSQL.

## Вимоги

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL або PostgreSQL

## Перший запуск

1. Скопіювати `.env.example` у `.env`, прописати `DB_*` та інші змінні за потреби.
2. `composer install` і `npm install`.
3. `php artisan key:generate` — лише якщо `APP_KEY` у `.env` порожній. Повторний запуск змінить ключ і скине сесії.
4. `php artisan storage:link` (якщо ще немає `public/storage`).
5. `php artisan migrate` або `php artisan migrate --seed` для демо-даних.
6. Збірка фронту: `npm run dev` (розробка) або `npm run build` (прод).

Під час роботи достатньо підняти БД, `php artisan serve` (або віртуальний хост у XAMPP) і за потреби `npm run dev`. Міграції та сидер не потрібні на кожен старт.

`migrate:fresh` і `migrate:refresh` повністю очищають таблиці — використовувати лише свідомо.

## Деплой і `start.sh`

На сервері зручно викликати `start.sh`: він проганяє `migrate` (нові міграції без повного скидання БД). Змінна `SEED_ON_START=true` додає `db:seed` після старту; сидер доповнює відсутні записи, не перезаписуючи існуючих користувачів.

## Фото товарів

Якщо на проді не підвантажуються картинки (CDN, мережеві обмеження), можна залити зовнішні URL у локальне сховище й оновити шляхи в БД:

```bash
php artisan shop:mirror-product-images
php artisan shop:mirror-product-images --force
```

Через `start.sh`: `MIRROR_IMAGES_ON_START=true` для одноразового прогону, `MIRROR_IMAGES_FORCE=true` для примусового перезавантаження. Після успіху варто вимкнути автозапуск, щоб не ганяти команду на кожен деплой.

**Cloudinary** (зручно для продакшену): синхронізація в хмару та оновлення `product_images.path` на `secure_url`:

```bash
php artisan shop:sync-product-images-to-cloudinary
php artisan shop:sync-product-images-to-cloudinary --force
```

Змінні середовища: `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`, опційно `CLOUDINARY_FOLDER` (наприклад `brickshop/products`). Для `start.sh`: `CLOUDINARY_SYNC_ON_START`, опційно `CLOUDINARY_SYNC_FORCE`.

## Production

Після `composer install --no-dev` і застосування міграцій:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Що в проєкті

- Каталог з пошуком і фільтрами
- Сторінка товару, галерея, відгуки
- Кошик, оформлення (у т.ч. гість), оплата (LiqPay тощо за конфігом)
- Профіль, обране, бонуси (де увімкнено)
- Адмін-панель
- Локалізація (кілька мов)
