# Brick Shop (LEGO Store)

Full-featured LEGO-themed e-commerce built on Laravel 10, PHP 8.2+, Blade, and Vite.

## Requirements
- PHP 8.2+
- Composer
- Node.js 18+
- XAMPP (Apache + MySQL + phpMyAdmin)

## Quick Start (XAMPP)
1. Start Apache and MySQL in XAMPP.
2. Create a database, e.g. `brick_shop`.
3. Copy `.env.example` to `.env` and set:
   - `APP_URL=http://localhost/lego-shop3/public`
   - `DB_DATABASE=brick_shop`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=`
4. Install dependencies:
   - `composer install`
   - `npm install`
5. Build assets:
   - `npm run build` (or `npm run dev`)
6. Generate key:
   - `php artisan key:generate`
7. Link storage:
   - `php artisan storage:link`
8. Run migrations and seeders:
   - `php artisan migrate --seed`

## Demo Accounts
- Admin: `admin@brickshop.test` / `Admin123!`
- User: `user@brickshop.test` / `User123!`

## Mail Setup (Password Reset)
Set in `.env`:
```
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_pass
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@brickshop.test"
MAIL_FROM_NAME="Brick Shop"
```

Dev options:
- Use `MAIL_MAILER=log` to log emails in `storage/logs/laravel.log`
- Or use Mailpit/Mailhog and point SMTP there

## Support Widget Settings
Admin panel → Settings:
- `phone_support`
- `telegram_support_url`
- `show_support_widget`

Seeder default values are in `database/seeders/SettingSeeder.php`.

## Translations
Locale files are in `lang/{uk,en,pl,ru}`.  
Add new UI strings to `lang/*/messages.php` and validation to `lang/*/validation.php`.

## Tests
Run tests:
```
php artisan test
```

## Key Structure
- `app/Http/Controllers` — MVC controllers
- `app/Models` — Eloquent models
- `database/migrations` — schema
- `database/seeders` — demo data
- `resources/views` — Blade templates
- `lang` — localization

## Commands Summary
```
composer install
npm install
npm run build
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
```
