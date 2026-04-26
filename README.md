# Invoice Tracking System

A **Laravel** web app for **sales and purchase invoices**, **clients** and **suppliers**, with a **dashboard**, **outstanding summaries**, **PDF/Excel exports**, and **database notifications** for overdue items. Each account’s data is scoped by `user_id` (multi-tenant style).

---

## Features

- **Dashboard** — totals, client/supplier counts, recent invoices  
- **Sales & purchase** — line items, statuses (`pending` / `overdue` / `paid`), mark paid, PDF & Excel per invoice  
- **Clients & suppliers** — credit periods, per-party pending/overdue statements (PDF/Excel)  
- **Reports** — outstanding summaries with optional PDF export  
- **Admin users** — CRUD, `is_admin`, module **permissions** (`dashboard`, `sales`, `purchase`, `clients`, `suppliers`, `reports`), **active/inactive** accounts  
- **Notifications** — overdue sale/purchase alerts (in-app)  
- **Profile** — name, email, password (Fortify), optional profile photo  

---

## Tech stack

| Area | Choice |
|------|--------|
| Framework | **Laravel 13**, **PHP 8.3+** |
| Auth | **Laravel Fortify** (login, profile, password change) |
| UI | **Blade**, **Bootstrap 5**, **Font Awesome**, **DataTables** (server-side) |
| PDF | **barryvdh/laravel-dompdf** |
| Spreadsheets | **maatwebsite/excel** |
| Tooling | **Vite**, **Pint**, **PHPUnit** |

Validation for writes uses **Form Request** classes under `app/Http/Requests/`.

---

## Authentication & access

- **Email + password** only (no OAuth). **No public registration** — admins create users under `/admin/users`.
- **No forgot-password / email reset** in this build. **Email verification** is not used.
- **`is_admin`**: full module access + user management.
- **Non-admins**: JSON **`permissions`** on `users`; at least one module (or admin) is required for meaningful access.
- **`is_active`**: inactive users cannot sign in; middleware blocks authenticated inactive users.
- After login, redirect goes to the **first allowed module** (or **Profile** if none).

| Permission key | Routes / area |
|----------------|----------------|
| `dashboard` | `/dashboard` |
| `sales` | Sales CRUD, exports |
| `purchase` | Purchase CRUD, exports |
| `clients` | Clients CRUD, statements |
| `suppliers` | Suppliers CRUD, statements |
| `reports` | `/reports`, summary PDFs |

---

## Screenshots

### Login

![Login page](docs/screenshots/login.png)

### Dashboard

![Dashboard](docs/screenshots/dashboard.png)

### Clients

![Clients](docs/screenshots/clients.png)

*(Add more images under `docs/screenshots/` if you like.)*

---

## Requirements

- **PHP** `^8.3` with extensions: `pdo_mysql` (or `pdo_sqlite`), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`; `zip` / `gd` help Excel/PDF  
- **Composer** 2.x  
- **Node.js** 18+ and **npm** (for Vite when editing bundled assets)  
- **MySQL** (recommended for production) or **SQLite** for local/demo  

---

## Quick start

### 1. Clone and install

```bash
git clone https://github.com/YOUR_USERNAME/invoice-tracking-system.git
cd invoice-tracking-system
composer install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` — at minimum set `APP_URL` and database:

**MySQL (typical)**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice_tracker
DB_USERNAME=root
DB_PASSWORD=
```

**SQLite (quick local try)**

```env
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite
```

Then create the file if needed: `touch database/database.sqlite`

### 3. Migrate and seed admin

```bash
php artisan migrate
php artisan db:seed
```

Default admin (from `Database/Seeders/DatabaseSeeder.php`):

| Field | Value |
|-------|--------|
| Email | `admin@example.com` |
| Password | `password` |

`db:seed` is **idempotent** for that email (`updateOrCreate`). **Change this password before any shared or production deploy.**

### 4. Storage (profile photos)

```bash
php artisan storage:link
```

### 5. Run

```bash
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000), sign in, then use **Users** (admin) to add staff and assign modules.



```bash
npm install
npm run build
# or: npm run dev

Add your name, portfolio, or LinkedIn here when you publish the repo.
