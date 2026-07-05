# Praise U Lord

Online Bible and Faith Services web application for Roman Catholic communities. Public pages use an EveryPsalm-inspired design; the superadmin panel uses AdminLTE.

## Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13, REST API, Laravel Sanctum |
| Database | MySQL |
| Frontend | Vue 3, Vue Router, Pinia, Vite |
| UI (Public) | Custom EveryPsalm-inspired layout |
| UI (Admin) | AdminLTE 3, Bootstrap 5, jQuery (modal mixins) |

## Features

### Public site
- **Bible** — Old & New Testament across many languages and translations (English Catholic RSVCE/NABRE/KJV/DR, plus Arabic, Chinese, German, Spanish, Portuguese, Tagalog, Cebuano, and more)
- **Mass Guide** — Full order of Mass with priest and people responses, plus daily readings by liturgical date
- **Fiesta Calendar** — Monthly calendar of feast days for Jesus Christ, the Holy Trinity, Mary, saints, and angels (fixed and movable feasts)
- **Novenas** — Nine-day devotions with leader and congregation responses (Our Lady of Perpetual Help, Sacred Heart, Santo Niño, St. Jude, etc.)
- **Prayers** — Regular and common Catholic prayers
- **AI Advice** — Ask questions; answers grounded in Bible verse search
- **Daily Proverb** — Random sidebar widget
- **Daily Psalm** — Random banner section
- **Advertisements** — Below banner, sidebars, footer
- **Donations** — Bank, PayPal, e-wallet info in sidebars and Contact page
- **Static pages** — Privacy Policy, Terms, About Us, Contact Us
- **Social media** — Configurable share/follow links

### Admin panel (`/admin`)
- Dashboard with recent visits, most visited content, total visitors
- Full CRUD for every feature with **search, sort, filters, pagination**
- **Add / Export / Import** on every resource
- **Edit / Delete / Restore / Force Delete** per row
- All forms in **Bootstrap modals**
- Advertisement sales reports and invoicing
- System settings (header/footer colors, date/temperature display)
- Donation, social media, and ad management

## Requirements

See **[INSTALL.md](INSTALL.md)** for full system requirements and step-by-step guides (localhost, Linux VPS, cPanel).

| Requirement | Version |
|-------------|---------|
| PHP | 8.3+ |
| MySQL | 8.0+ |
| Composer | 2.x |
| Node.js | 18+ (build assets) |

PHP extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`

## Installation

### Browser setup wizard (recommended)

After uploading files and running Composer, open your site in a browser. You are redirected to **`/install`** — a WordPress-style wizard that checks requirements, connects MySQL, and creates your admin account.

Detailed instructions for **localhost**, **Linux cloud servers**, and **cPanel** are in **[INSTALL.md](INSTALL.md)**.

### Quick install (localhost / CLI)

```bash
# Clone and enter project
cd praise-u-lord

# Install dependencies
composer install
npm install

# Environment (wizard can fill DB settings later)
cp .env.example .env

# Create database
mysql -u root -p -e "CREATE DATABASE praise_u_lord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Start dev servers and open http://127.0.0.1:8888/install
npm start
```

Or configure manually:

```bash
php artisan key:generate
# Configure MySQL in .env, then:
php artisan migrate
php artisan db:seed
touch storage/app/installed.lock
npm run build
```

## Development

> **Important:** Do **not** open `http://localhost:5173` in your browser. That is only the Vite asset server.  
> Open the **Laravel URL** instead — that is where the public pages and admin panel live.

> **Port note:** If port 8000 is used by another project, this app uses **port 8888**.

### Quick start (recommended)

```bash
npm start
```

This runs Laravel (`php artisan serve` on port 8888) and Vite together.

### Manual start

```bash
# Terminal 1 — Laravel (required for pages to work)
php artisan serve --host=127.0.0.1 --port=8888

# Terminal 2 — Vite (required for hot reload in dev)
npm run dev
```

Visit:
- **Public site:** http://127.0.0.1:8888
- **Admin panel:** http://127.0.0.1:8888/admin/login

If you accidentally open `http://localhost:5173`, you will be redirected to the Laravel app automatically.

### Default superadmin

| Field | Value |
|-------|-------|
| Email | `admin@praiseulord.com` |
| Password | `password` |

Change this password immediately in production.

## Scheduled tasks

Mass guides are refreshed yearly on January 1:

```bash
php artisan mass-guides:fetch          # current year
php artisan mass-guides:fetch 2027     # specific year
php artisan fiestas:seed               # Catholic fiesta calendar (95+ feasts)
```

Readings are generated for every day of the year with full passage text pulled from the seeded Bible. Major feasts include complete solemnity readings. The public Mass Guide page supports date selection and previous/next navigation.

Add to cron:

```bash
* * * * * cd /path/to/praise-u-lord && php artisan schedule:run >> /dev/null 2>&1
```

## Bible data import

Full Bible text is imported automatically during seeding from public-domain sources:

- **English (Catholic)** — RSVCE, NABRE, KJV (66 books + deuterocanonical from Douay-Rheims), Douay-Rheims (DR)
- **English (Protestant)** — Basic English Bible (BBE)
- **World languages** — Arabic, Chinese, German, Greek, Esperanto, Spanish, Finnish, French, Korean, Portuguese, Romanian, Russian, Vietnamese (via [thiagobodruk/bible](https://github.com/thiagobodruk/bible))
- **Tagalog / Filipino** — Ang Dating Biblia (ADB, 1905 public domain)
- **Cebuano** — Ang Biblia Pinadayag (CEB, public domain via CCEL)

On the public Bible page, readers choose **Language → Translation → Testament**. Catholic translations include all 73 books; most other translations use the 66-book Protestant canon.

Re-import all verse text:

```bash
php artisan bible:import-text          # all versions
php artisan bible:import-text ADB      # one version (KJV, CEB, RVR, etc.)
```

You can also import custom JSON via:

1. **Admin panel** — Export/Import JSON on Bible Versions, Books, Chapters, Verses
2. **Books structure** — Import `books_structure.json` on Bible Books (creates all 73 books and chapter shells)

Structure files:

```
database/seeders/data/bible/
├── versions.json
├── books_structure.json           # 73-book Catholic canon
├── books_structure_protestant.json
├── book_name_locales.json         # Tagalog/Cebuano book titles
├── rsvce_chapters.json            # legacy sample chapters (optional)
├── nabre_chapters.json
└── kjv_chapters.json
```

Source downloads are cached in `storage/app/bible-cache/`.

## API overview

| Prefix | Auth | Purpose |
|--------|------|---------|
| `/api/public/*` | None | Public content, visits, contact, AI |
| `/api/auth/*` | Sanctum | Login, logout, me |
| `/api/*` | Sanctum + superadmin | Admin CRUD, dashboard |

Admin resources support: `GET`, `POST`, `PUT/PATCH`, `DELETE`, `POST .../restore`, `DELETE .../force`, `GET .../export`, `POST .../import`.

## Project structure

```
app/
├── Console/Commands/FetchMassGuidesCommand.php
├── Http/Controllers/Api/          # REST controllers
├── Http/Controllers/Traits/HandlesCrud.php
├── Models/
└── Services/                      # Bible AI, Mass Guide fetch, Analytics

resources/js/
├── layouts/                       # PublicLayout, AdminLayout
├── pages/public/                  # Public pages
├── pages/admin/                   # Admin CRUD pages
├── components/shared/             # DataTable, modals, CRUD toolbar
├── router/
└── stores/

database/seeders/                  # All seeders + Bible JSON data
```

## Security

- Laravel Sanctum token authentication for admin API
- `superadmin` middleware on all admin routes
- CSRF protection on web routes
- Password hashing via bcrypt
- SQL injection protection via Eloquent
- Input validation on all API endpoints

## License

Proprietary — Praise U Lord project.
