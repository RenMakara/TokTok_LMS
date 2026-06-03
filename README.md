# TokTok Library Management System

TokTok Library Management System is a Laravel 11 web application for managing library members, books, borrowing records, returns, and basic library reporting. The system uses a simple role-based flow for administrators and members, with a Khmer-friendly interface and PDF export support.

## Features

- Member login using library member ID
- Admin dashboard with library summary data
- Member management with create, edit, delete, search, print preview, and PDF export
- Book management with categories, authors, ISBN, stock, search, create, edit, and delete
- Borrow record management with checkout, return, and due-date extension
- Member check-in flow for returning books
- Global search endpoint
- SweetAlert feedback for user actions
- Animated login UI
- Dockerized web app with PostgreSQL

## Tech Stack

- Laravel 11
- PHP 8.3
- PostgreSQL
- Blade templates
- Tailwind CSS 4
- Vite
- Apache inside Docker for the web image
- Docker Compose for local app and database services
- DomPDF for PDF generation

## Main Roles

### Admin

Admins can access the dashboard and manage users, books, and borrow records.

### Member

Members can access the check-in flow after logging in with their member ID.

## Requirements

For local development without Docker:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- PostgreSQL
- PHP PostgreSQL extension

On Ubuntu, install the PHP PostgreSQL extension with:

```bash
sudo apt install php-pgsql
```

For Docker development:

- Docker
- Docker Compose

## Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Default local database values:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=toktok_lms
DB_USERNAME=toktok
DB_PASSWORD=secret
```

Generate the Laravel app key:

```bash
php artisan key:generate
```

## Run With Docker

Build the Laravel web image:

```bash
docker compose build web
```

Start the web app and PostgreSQL:

```bash
docker compose up -d web
```

Open the application:

```text
http://localhost:8080
```

The Docker web service will:

- build production Vite assets
- install Composer dependencies without dev packages
- serve Laravel through Apache
- wait for PostgreSQL to be ready
- run migrations
- run seeders

Useful Docker commands:

```bash
docker compose ps
docker compose logs -f web
docker compose exec web php artisan route:list
docker compose exec web php artisan migrate:fresh --seed
docker compose down
```

To remove the local PostgreSQL database volume:

```bash
docker compose down -v
```

## Run Without Docker

Start PostgreSQL first, then run:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

For frontend development, run Vite in another terminal:

```bash
npm run dev
```

## Seed Data

The database seeders create:

- default admin user
- default book categories

The admin seeder is safe to run more than once because it uses `updateOrCreate`.

On a fresh database, the seeded admin member ID is usually:

```text
10001
```

Use this ID on the login page.

## Common Commands

Run tests:

```bash
php artisan test
```

Build frontend assets:

```bash
npm run build
```

Clear Laravel caches:

```bash
php artisan optimize:clear
```

Reset the local database:

```bash
php artisan migrate:fresh --seed
```

## Project Structure

Important folders:

```text
app/Http/Controllers     Main request handling logic
app/Models               Eloquent models
database/migrations      Database schema
database/seeders         Default seed data
resources/views          Blade UI templates
resources/css            Tailwind entry CSS
resources/js             JavaScript entry files
docker/                  Apache and entrypoint config for Docker
public/                  Public assets and Laravel entrypoint
```

## Docker Image Notes

The web image is built from a multi-stage Dockerfile:

- Node stage builds Vite assets
- PHP CLI stage installs Composer dependencies
- PHP Apache stage serves the Laravel app from `public/`

Image name:

```text
toktok-lms-web:latest
```

The app container listens on port `80` internally and is exposed on local port `8080`.

## Deployment Notes

For production, change these values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_PASSWORD=use-a-strong-password
```

Also change `POSTGRES_PASSWORD` in `docker-compose.yml` if using Docker PostgreSQL in production.

After deployment, run:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Never point a web server to the project root. The web root must be:

```text
public/
```

## Troubleshooting

If Laravel cannot connect to PostgreSQL, check:

```bash
php -m | grep pgsql
docker compose ps
docker compose logs postgres
```

If seeders fail because records already exist, run:

```bash
php artisan db:seed
```

If you want a completely fresh local database:

```bash
php artisan migrate:fresh --seed
```

If frontend assets are missing:

```bash
npm run build
```

For Docker, rebuild the web image:

```bash
docker compose build web
docker compose up -d web
```
