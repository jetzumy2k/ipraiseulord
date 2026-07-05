# Praise U Lord — Installation Guide

This guide walks you through installing **Praise U Lord** on **localhost**, a **Linux cloud server (VPS)**, or **cPanel shared hosting**. A browser-based **Setup Wizard** (similar to WordPress) is included for the final configuration step.

---

## Table of contents

1. [System requirements](#system-requirements)
2. [Quick overview](#quick-overview)
3. [Install on localhost (Windows, Mac, Linux)](#install-on-localhost)
4. [Install on a Linux cloud server (VPS)](#install-on-a-linux-cloud-server-vps)
5. [Install on cPanel shared hosting](#install-on-cpanel-shared-hosting)
6. [Using the Setup Wizard](#using-the-setup-wizard)
7. [After installation](#after-installation)
8. [Troubleshooting](#troubleshooting)

---

## System requirements

### Server

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| **PHP** | 8.3 | 8.3 or 8.4 |
| **MySQL** | 8.0 | 8.0+ or MariaDB 10.6+ |
| **Web server** | Apache 2.4+ or Nginx 1.18+ | With URL rewriting enabled |
| **RAM** | 512 MB | 1 GB+ (Bible import uses more memory) |
| **Disk space** | 500 MB | 2 GB+ (Bible text and cache) |

### PHP extensions (required)

Enable these in `php.ini` or through your hosting panel:

- `pdo_mysql` — database connection
- `mbstring` — text handling
- `openssl` — encryption
- `tokenizer` — Laravel framework
- `xml` or `dom` — XML parsing
- `ctype` — character checks
- `json` — JSON handling
- `bcmath` — numeric operations
- `fileinfo` — file uploads
- `curl` — remote Bible source downloads during seeding

### Software (for deployment)

| Tool | Required for | Notes |
|------|--------------|-------|
| **Composer 2** | All installs | Installs PHP dependencies |
| **Node.js 18+** | Building frontend | Run on your computer or CI; upload built files to production |
| **npm** | Building frontend | `npm install && npm run build` |
| **Git** | Optional | Clone the repository |

### Writable directories

The web server user must be able to write to:

```
storage/
storage/app/
storage/framework/
storage/logs/
bootstrap/cache/
.env (file or project root folder)
```

### Production frontend assets

Production servers need compiled assets in `public/build/`. Build them **before** upload or on the server if Node.js is available:

```bash
npm install
npm run build
```

The Setup Wizard checks for `public/build/manifest.json`. In local development you may skip this if Vite is running (`npm run dev`).

---

## Quick overview

Every installation follows the same pattern:

1. **Upload or clone** the project files
2. **Install PHP dependencies** with Composer
3. **Build frontend assets** with npm (production)
4. **Create a MySQL database** and user
5. **Set folder permissions** on `storage` and `bootstrap/cache`
6. **Point the web root** to the `public/` folder
7. **Open the site in a browser** → you are redirected to the **Setup Wizard** at `/install`
8. Complete the wizard (requirements → database → admin account)
9. **Configure cron** and secure the site

---

## Install on localhost

Best for development on Windows (WSL/XAMPP/Laragon), macOS, or Linux.

### Step 1 — Install prerequisites

- [PHP 8.3+](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/download/)
- [Node.js 18+](https://nodejs.org/)
- [MySQL 8+](https://dev.mysql.com/downloads/)

### Step 2 — Clone and install dependencies

```bash
git clone <your-repo-url> praise-u-lord
cd praise-u-lord

composer install
npm install
```

### Step 3 — Prepare environment file

```bash
cp .env.example .env
```

You do **not** need to fill in database details manually — the Setup Wizard will do that. For local dev you can leave defaults.

### Step 4 — Create an empty database (optional)

The wizard can use a database you create beforehand:

```bash
mysql -u root -p -e "CREATE DATABASE praise_u_lord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 5 — Start the development servers

```bash
npm start
```

This runs Laravel on **http://127.0.0.1:8888** and Vite for hot reload.

> **Important:** Always open **http://127.0.0.1:8888** in your browser, not port 5173 (Vite only serves assets).

### Step 6 — Run the Setup Wizard

1. Visit **http://127.0.0.1:8888**
2. You are redirected to **http://127.0.0.1:8888/install**
3. Follow the steps: Welcome → Requirements → Database → Site & Admin → Install
4. When finished, log in at **http://127.0.0.1:8888/admin/login**

### Local development without the wizard

If you prefer the command line (e.g. you already have `.env` configured):

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
touch storage/app/installed.lock
npm run dev   # separate terminal, with php artisan serve
```

Default superadmin (when using `db:seed` without wizard): `admin@praiseulord.com` / `password`

---

## Install on a Linux cloud server (VPS)

Examples: DigitalOcean, Linode, AWS EC2, Vultr, Hetzner with Ubuntu 22.04/24.04.

### Step 1 — Server packages

```bash
sudo apt update
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-bcmath php8.3-zip php8.3-cli unzip git
```

Install Composer:

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Install Node.js (for building assets on the server, or build locally and upload):

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Step 2 — Create MySQL database

```bash
sudo mysql
```

```sql
CREATE DATABASE praise_u_lord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'praise_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON praise_u_lord.* TO 'praise_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3 — Deploy application files

```bash
cd /var/www
sudo git clone <your-repo-url> praise-u-lord
cd praise-u-lord

composer install --no-dev --optimize-autoloader
npm install
npm run build

cp .env.example .env
```

### Step 4 — Permissions

```bash
sudo chown -R www-data:www-data /var/www/praise-u-lord
sudo chmod -R 775 storage bootstrap/cache
```

### Step 5 — Nginx configuration

Create `/etc/nginx/sites-available/praise-u-lord`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/praise-u-lord/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
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
        fastcgi_read_timeout 600;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site and reload Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/praise-u-lord /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### Step 6 — SSL (recommended)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Step 7 — Run the Setup Wizard

1. Open **https://yourdomain.com** in your browser
2. Complete **/install** with your database credentials and admin account
3. Use **Environment: Production** and leave **Debug mode** unchecked

### Step 8 — Cron job

```bash
sudo crontab -u www-data -e
```

Add:

```cron
* * * * * cd /var/www/praise-u-lord && php artisan schedule:run >> /dev/null 2>&1
```

---

## Install on cPanel shared hosting

Most cPanel hosts support Laravel if PHP 8.3+ and MySQL are available.

### Step 1 — Check hosting requirements

In cPanel → **Select PHP Version** (or **MultiPHP Manager**):

- PHP **8.3** or higher
- Enable extensions listed in [System requirements](#system-requirements)

### Step 2 — Create MySQL database

In cPanel → **MySQL Databases**:

1. Create a database (e.g. `cpanel_praise`)
2. Create a user with a strong password
3. Add the user to the database with **ALL PRIVILEGES**
4. Note: **host** is usually `localhost`, not your domain name

### Step 3 — Upload files

**Option A — Git (if available in cPanel)**

Clone into a folder outside `public_html`, e.g. `/home/username/praise-u-lord`

**Option B — ZIP upload**

1. On your computer run `composer install --no-dev` and `npm run build`
2. ZIP the project (exclude `node_modules`, `.git`)
3. Upload via **File Manager** and extract to `/home/username/praise-u-lord`

### Step 4 — Point domain to `public/`

**Recommended:** Map document root to the Laravel `public` folder.

In cPanel → **Domains** → your domain → **Document Root**:

Set to: `/home/username/praise-u-lord/public`

If your host does **not** allow changing the document root:

1. Copy contents of `public/` into `public_html/`
2. Edit `public_html/index.php` and update paths:

```php
require __DIR__.'/../praise-u-lord/vendor/autoload.php';
$app = require_once __DIR__.'/../praise-u-lord/bootstrap/app.php';
```

(Adjust `../praise-u-lord` to match your folder structure.)

### Step 5 — Install Composer dependencies

Via **Terminal** in cPanel (if available):

```bash
cd ~/praise-u-lord
composer install --no-dev --optimize-autoloader
cp .env.example .env
```

If Terminal is not available, run `composer install` locally and upload the `vendor/` folder.

**Build frontend on the server (optional):** Prefer building on your computer and uploading `public/build/`. If you must build on cPanel, use the project Terminal (not the Node.js “Run NPM Install” button):

```bash
cd ~/ipraiseulord
npm run install:server
npm run build
```

Do **not** use cPanel’s **Setup Node.js App → Run NPM Install** alone — it installs into a separate virtualenv and can trigger `husky: command not found` errors.

### Step 6 — Permissions

In **File Manager**, set permissions:

- `storage/` → **775** (recursive)
- `bootstrap/cache/` → **775** (recursive)

### Step 7 — Run the Setup Wizard

1. Visit **https://yourdomain.com**
2. You should see the Setup Wizard at `/install`
3. Database settings for cPanel:

| Field | Typical value |
|-------|----------------|
| Host | `localhost` |
| Port | `3306` |
| Database name | `cpanel_praise` (full name with prefix) |
| Username | `cpanel_user` (full name with prefix) |
| Password | (password you created) |

4. **Site URL** must match your live URL, e.g. `https://yourdomain.com`
5. Choose **Production** environment

### Step 8 — Cron job in cPanel

cPanel → **Cron Jobs** → add every minute:

```bash
cd /home/username/praise-u-lord && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

Use **PHP CLI path** from cPanel (often `/usr/local/bin/php` or `/opt/cpanel/ea-php83/root/usr/bin/php`).

### cPanel tips

- Increase **PHP max execution time** to **600** seconds for the first install (Bible seeding is slow)
- Increase **memory_limit** to **256M** or **512M**
- If `/install` shows a blank page, check `storage/logs/laravel.log` in File Manager

---

## Using the Setup Wizard

The wizard runs at **`/your-site/install`** until installation completes.

| Step | What it does |
|------|----------------|
| **1. Welcome** | Introduction |
| **2. Requirements** | Checks PHP version, extensions, folder permissions, built assets |
| **3. Database** | Test MySQL connection (create the database first) |
| **4. Site & Admin** | Site name, URL, environment, superadmin account |
| **5. Install** | Writes `.env`, runs migrations, seeds Bible and sample content |
| **6. Done** | Links to public site and admin login |

After success, `/install` is disabled and redirects to the homepage.

**Re-running installation:** Delete `storage/app/installed.lock` and empty or drop the database tables only if you intend a full reset. This is destructive.

---

## After installation

### 1. Log in to admin

- URL: `https://yourdomain.com/admin/login`
- Use the email and password you set in the wizard

### 2. Security checklist

- [ ] Change the default admin password if you used CLI seeding
- [ ] Set `APP_DEBUG=false` in production (wizard does this when Environment = Production)
- [ ] Use HTTPS
- [ ] Restrict file permissions: `.env` should not be world-readable
- [ ] Set up cron for scheduled tasks (Mass Guide updates, etc.)

### 3. Optimize Laravel (production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Scheduled tasks

The app refreshes Mass Guides yearly and supports fiesta seeding. Ensure cron runs every minute (see Linux/cPanel sections above).

Manual commands:

```bash
php artisan mass-guides:fetch
php artisan fiestas:seed
```

### 5. Bible re-import (optional)

```bash
php artisan bible:import-text
```

---

## Troubleshooting

### “npm ERESOLVE” or bootstrap peer dependency warnings

`admin-lte` pulls in older packages (`bootstrap-switch`) that expect Bootstrap 3, while this project uses Bootstrap 5. The repo includes an `.npmrc` with `legacy-peer-deps=true` and `package.json` overrides so `npm install` works on shared hosting.

On your server:

```bash
cd ~/ipraiseulord   # your project folder
rm -rf node_modules
npm run install:server
npm run build
```

Or run the flags directly:

```bash
npm install --legacy-peer-deps --ignore-scripts
npm run build
```

### “husky: command not found” (error code 127)

AdminLTE depends on **summernote**, whose newer releases run `husky install` after install. That fails on cPanel and most shared hosts because Husky is a dev-only Git hook tool.

This project fixes that in two ways:

1. **`summernote` is pinned to 0.8.18** via `package.json` overrides (no postinstall script).
2. **`.npmrc` sets `ignore-scripts=true`** as a fallback.

Always install from your **project folder** (where `package.json` and `.npmrc` live), not only via cPanel’s “Run NPM Install” button — that can install into a separate Node virtualenv and skip project settings.

**Recommended on cPanel:** build assets on your computer, then upload only `public/build/` to the server and skip `npm install` on the server entirely.

If you must build on the server:

```bash
cd ~/ipraiseulord
rm -rf node_modules
npm run install:server
npm run build
```

Deprecation warnings (lodash, bootstrap 4, etc.) are safe to ignore — they come from AdminLTE dependencies and do not block the build.

### “Failed to resolve import @popperjs/core” during `npm run build`

Bootstrap 5 requires `@popperjs/core` for dropdowns, modals, and tooltips. It is listed as a direct dependency in this project. If you see this error on cPanel, pull the latest `package.json` and reinstall:

```bash
cd ~/ipraiseulord
npm run install:server
npm run build
```

### LiteSpeed or cPanel shows “404 Not Found” at `/install`

If the error page says **“Proudly powered by LiteSpeed Web Server”** (not a Laravel page), the request never reached the app. Fix the server setup:

1. **Set document root to `public/`** (recommended)  
   cPanel → **Domains** → your domain → **Document Root** →  
   `/home/username/praise-u-lord/public`

2. **If you cannot change document root**, upload the project so `public/` contents live in `public_html/`, and edit `public_html/index.php` paths as described in [Step 4 — Point domain to `public/`](#step-4--point-domain-to-public).

3. **Enable URL rewriting**  
   - cPanel → **MultiPHP INI Editor** or **Select PHP Version** → ensure `.htaccess` / mod_rewrite is allowed  
   - The repo includes a root `.htaccess` that forwards requests to `public/` when the document root is the project folder

4. **Verify Laravel is responding**  
   Visit `https://yourdomain.com/up` — you should see `{"status":"ok"}` or similar, not a LiteSpeed 404.

5. **Upload required files** — ensure `vendor/`, `.env` (or `.env.example`), and `public/build/` are on the server after `composer install` and `npm run build`.

### Redirect loop or blank page at `/install`

- Confirm `storage/` and `bootstrap/cache/` are writable
- Check `storage/logs/laravel.log`
- Ensure `public/build/manifest.json` exists (run `npm run build`)

### “Database connection failed”

- Verify database name, user, and password in cPanel/MySQL
- On cPanel, use `localhost` as host
- Ensure the database exists before running the wizard

### Installation timeout

Bible seeding can take 5–15 minutes. Increase:

- PHP `max_execution_time` (600 recommended for first install)
- Nginx `fastcgi_read_timeout`
- cPanel PHP settings

Then reload `/install` and run again after fixing permissions, or use CLI:

```bash
php artisan migrate --force
php artisan db:seed --force
touch storage/app/installed.lock
```

### 500 error after install

```bash
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

### “Application is already installed” but you need the wizard

Remove the lock file only if you intend to reinstall:

```bash
rm storage/app/installed.lock
```

### Existing manual install not redirecting to wizard

If you migrated and seeded manually, the app auto-detects an existing database and skips the wizard. To force the wizard, drop all tables and remove `storage/app/installed.lock`.

---

## Support files

| File | Purpose |
|------|---------|
| `.env.example` | Environment template |
| `storage/app/installed.lock` | Marks installation complete |
| `README.md` | Project overview and development notes |

For development commands, API overview, and Bible import details, see **README.md**.
