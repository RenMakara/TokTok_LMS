# TokTok Library Management System

TokTok is a Laravel 11 library management system for managing members, books, borrow records, check-ins, and PDF exports.

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- Docker and Docker Compose
- PostgreSQL PHP extension (`php-pgsql` on Ubuntu)

## Local Setup

On Ubuntu, install the PHP Postgres extension first:

```bash
sudo apt install php-pgsql
```

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
docker compose up -d postgres
php artisan migrate --seed
npm run build
php artisan serve
```

Open the app at `http://127.0.0.1:8000`.

For Vite development assets, run this in another terminal:

```bash
npm run dev
```

## Current Notes

- This project requires Laravel through Composer; Laravel is not a separate global install.
- The default `.env.example` uses Postgres on `127.0.0.1:5432`.
- Docker Compose starts a local Postgres database named `toktok_lms` with username `toktok` and password `secret`.
- Seeders are safe to rerun with `php artisan db:seed`; they update or reuse existing admin/category records.
- If you want to delete all local database data and start fresh, run `php artisan migrate:fresh --seed`.
- Use `docker compose down` to stop Postgres, or `docker compose down -v` to stop it and delete the local database volume.
- On a fresh machine, install PHP and Composer before running any `php artisan` commands.

## Deploy To Ubuntu VPS

This guide assumes:

- Ubuntu server with SSH access
- Nginx
- PHP-FPM
- Postgres running from this project's Docker Compose file
- Project path: `/var/www/toktok`
- Domain example: `example.com`

Install server packages:

```bash
sudo apt update
sudo apt install -y nginx git unzip curl composer nodejs npm \
  php-fpm php-cli php-mbstring php-xml php-bcmath php-curl php-zip php-pgsql php-gd
```

Clone and install the project:

```bash
sudo mkdir -p /var/www/toktok
sudo chown -R $USER:$USER /var/www/toktok
git clone <your-repo-url> /var/www/toktok
cd /var/www/toktok

cp .env.example .env
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate
```

Edit `.env` for production:

```env
APP_NAME="TokTok LMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=toktok_lms
DB_USERNAME=toktok
DB_PASSWORD=change-this-password
```

Before starting Postgres in production, change `POSTGRES_PASSWORD` in `docker-compose.yml` to match `.env`.

Start Postgres and run Laravel setup:

```bash
docker compose up -d postgres
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

Create the Nginx site:

```bash
sudo nano /etc/nginx/sites-available/toktok
```

Use this config:

```nginx
server {
    listen 80;
    server_name example.com www.example.com;
    root /var/www/toktok/public;

    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/toktok /etc/nginx/sites-enabled/toktok
sudo nginx -t
sudo systemctl reload nginx
```

For HTTPS, install Certbot and request a certificate:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d example.com -d www.example.com
```

For future updates:

```bash
cd /var/www/toktok
git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload nginx
```
