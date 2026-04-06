# Деплой через GitHub

## 1. Репозиторій

1. Створи порожній репозиторій на GitHub (без README, якщо вже є локально).
2. Локально в корені проєкту:

```bash
git remote add origin https://github.com/<USER>/<REPO>.git
git branch -M main
git push -u origin main
```

Далі кожен `git push` у `main` оновлює код на GitHub.

## 2. CI (GitHub Actions)

Файл `.github/workflows/ci.yml` на кожен push/PR у `main`/`master` запускає PHPStan, PHPUnit і збірку Vite. Зелена галочка = код збирається і тести проходять.

## 3. Render (або інший хостинг)

1. У Render: **New → Web Service** → підключи **той самий** GitHub-репозиторій.
2. **Dockerfile** у корні — залиш як є.
3. **Environment**: `DATABASE_URL`, `DB_CONNECTION=pgsql`, `APP_KEY`, `APP_URL`, `SESSION_DRIVER=database`, `LOG_CHANNEL=stderr` (деталі — у картці PostgreSQL **Connections**).
4. Деплой на Render йде **автоматично** після push у підключену гілку.

Опційно в корені є `render.yaml` — можна застосувати через **Blueprints**, якщо зручніше, ніж ручні кроки.

## 4. Секрети

- Не коміть `.env` (він у `.gitignore`).
- Паролі БД і API-ключі тільки в змінних середовища на хостингу або в **GitHub Actions secrets**, якщо додаси workflow з деплоєм.
