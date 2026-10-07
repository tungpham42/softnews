# Newsroom CMS

A full-stack news website and admin CMS built with:

- Symfony 8.1 + Symfony CLI
- PHP 8.4+
- MySQL 8+
- Doctrine ORM + Migrations
- Symfony Security with session-based admin authentication
- React 19 + TypeScript + Vite
- Tailwind CSS 4 via the official Vite plugin
- Lucide SVG icons

## Features

Public site:
- Homepage with featured stories
- Category pages
- Tag filtering
- Post detail page
- Reader comments

Admin:
- Login/logout
- Dashboard
- Post CRUD
- Category CRUD
- Tag CRUD
- Comment moderation
- User list

## Project structure

```text
newsroom-cms/
├── backend/                 # Symfony API
│   ├── config/
│   ├── migrations/
│   ├── public/
│   ├── src/
│   ├── composer.json
│   └── .env.example
├── frontend/                # React/Vite UI
│   ├── src/
│   ├── package.json
│   └── .env.example
└── README.md
```

## Requirements

- PHP 8.4+
- Composer 2+
- Symfony CLI
- Node.js 20+
- npm 10+
- MySQL 8+

## 1. Create the database

```sql
CREATE DATABASE newsroom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 2. Configure Symfony

```bash
cd backend
cp .env.example .env.local
```

Set `DATABASE_URL` in `.env.local`.

Example:

```dotenv
DATABASE_URL="mysql://root:password@127.0.0.1:3306/newsroom?serverVersion=8.0&charset=utf8mb4"
```

Run migrations and fixtures:

```bash
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

Default admin login:

```text
Email: admin@newsroom.test
Password: admin12345
```

Change this password before production.

Start Symfony:

```bash
symfony server:start --port=8000
```

API base URL:

```text
http://127.0.0.1:8000/api
```

## 3. Configure React

```bash
cd ../frontend
cp .env.example .env.local
npm install
npm run dev
```

The Vite dev server runs on:

```text
http://127.0.0.1:5173
```

The Vite proxy forwards `/api` to Symfony at `http://127.0.0.1:8000`.

## 4. Production frontend build

```bash
npm run build
```

Copy the generated `frontend/dist` contents into your web hosting setup, or configure your reverse proxy to serve the React build and forward `/api` to Symfony.

## API summary

### Auth

```text
POST   /api/login
POST   /api/logout
GET    /api/me
```

### Posts

```text
GET    /api/posts
GET    /api/posts/{id}
POST   /api/posts
PUT    /api/posts/{id}
DELETE /api/posts/{id}
```

### Categories

```text
GET    /api/categories
POST   /api/categories
PUT    /api/categories/{id}
DELETE /api/categories/{id}
```

### Tags

```text
GET    /api/tags
POST   /api/tags
PUT    /api/tags/{id}
DELETE /api/tags/{id}
```

### Comments

```text
GET    /api/comments
POST   /api/posts/{id}/comments
PATCH  /api/comments/{id}/status
DELETE /api/comments/{id}
```

## Notes

This implementation intentionally uses a lightweight custom JSON API rather than API Platform so the React frontend has a straightforward CRUD contract and the Symfony side stays easy to customize.
