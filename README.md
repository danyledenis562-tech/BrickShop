# Brick Shop

Це мій навчальний проєкт інтернет‑магазину LEGO на Laravel.

## Що потрібно
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL або PostgreSQL

## Як запустити локально
1. Скопіювати `.env.example` у `.env` та вказати доступи до БД.
2. Встановити залежності:
   - `composer install`
   - `npm install`
3. Зібрати стилі:
   - `npm run dev` (або `npm run build`)
4. Згенерувати ключ:
   - `php artisan key:generate`
5. Зробити лінк на storage:
   - `php artisan storage:link`
6. Запустити міграції:
   - `php artisan migrate`

## Коротко про функціонал
- каталог товарів, пошук, фільтри
- сторінка товару з відгуками
- кошик і оформлення замовлення
- профіль користувача
- адмін‑панель
- кілька мов
