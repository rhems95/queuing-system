# PECIT Queuing System

Queue management system for **Philippine Electronics and Communication Institute of Technology Inc. (PECIT)**.

Customers get anonymous tickets from a public **kiosk**. Staff call numbers at service windows. A public **display** shows who is being served. **Admins** manage users, history, and reports.

**Stack:** Laravel 12 · Blade · Vite/Tailwind · MySQL/MariaDB (typical **XAMPP** on Windows)

---

## What the system does

| Module | Access | Purpose |
|--------|--------|---------|
| **Kiosk** | Public | 3-step ticket: service → priority → confirm → print |
| **Display** | Public | Now serving (by window group) + waiting list + optional voice |
| **Staff window** | Staff login | Call Next, Recall, Complete; live waiting list |
| **Staff float** | Staff | Small always-on-top Windows panel for the same actions |
| **Admin** | Admin login | Dashboard, users, history, tickets, reports |

- Tickets are **anonymous** (no student name/ID).
- Priority is **Regular** or **Priority** only.
- Kiosk UI is **finalized** — do not redesign it casually.
- Login / staff / admin use the **PECIT** navy/gold theme.
- Thermal tickets target **XP-58(XP-Q90EC)** (80mm thermal).

---

## How to run (XAMPP)

### 1. Prerequisites

- XAMPP (Apache + MySQL/MariaDB + PHP 8.2+)
- Composer
- Node.js / npm
- Google Chrome (for silent kiosk print and staff float)

### 2. Place the project

Example path:

```text
C:\xampp\htdocs\queue-system
```

### 3. Create the database

1. Start **Apache** and **MySQL** in XAMPP Control Panel.
2. Open phpMyAdmin → create database: `queuing_system`
3. Import the SQL dump:

```text
queuing_system.sql
```

(Or import via command line:)

```bash
mysql -u root -p queuing_system < queuing_system.sql
```

### 4. Configure `.env`

```bash
copy .env.example .env
```

Set at least:

```env
APP_NAME="PECIT Queuing System"
APP_URL=http://localhost/queue-system/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=queuing_system
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

> The SQL dump focuses on **domain tables**. Using `SESSION_DRIVER=file` and `CACHE_STORE=file` avoids needing Laravel `sessions` / `cache` tables on a fresh dump import.

Then:

```bash
composer install
npm install
npm run build
php artisan key:generate
```

### 5. Open the app

With Apache running, use (adjust if your path differs):

| Page | URL |
|------|-----|
| Kiosk | http://localhost/queue-system/public/kiosk |
| Display | http://localhost/queue-system/public/display |
| Login | http://localhost/queue-system/public/login |
| Staff | http://localhost/queue-system/public/window |
| Admin | http://localhost/queue-system/public/admin |

Optional (PHP built-in server from project root):

```bash
php artisan serve
```

Then open `http://127.0.0.1:8000/kiosk` (and so on).

---

## Users (from `queuing_system.sql`)

Roles: **`admin`** or **`staff`**. Staff must be assigned a **window**; only **one staff account per window**.

| Name | Email | Role | Window | Notes |
|------|-------|------|--------|--------|
| Admin | `admin@gmail.com` | admin | — | Password in dump is plain text **`admin`** (login accepts it) |
| rhem | `rhem@gmail.com` | staff | Cashier 1 (`window_id` 1) | Bcrypt hash in dump — reset via Admin → Users if unknown |
| omar | `omar@gmail.com` | staff | Cashier 2 (`window_id` 2) | Bcrypt hash in dump — reset via Admin → Users if unknown |

Create or edit users under **Admin → Users**. Staff accounts require a window; a window cannot have two staff users.

---

## Database overview

**Database name:** `queuing_system`  
**Canonical dump:** `queuing_system.sql` at the repo root

### Domain tables

| Table | Purpose |
|-------|---------|
| `services` | Service types + ticket prefix (`C`, `P`, `D`, `R`, …) |
| `windows` | Counters; `service_id`, `group_name` (display groups), `status` |
| `users` | `role` = `admin` \| `staff`; staff have `window_id` (unique when migration applied) |
| `daily_queue_counters` | Per-service daily serial for ticket numbers |
| `queues` | Tickets: number, service, priority (0/1), status, date (no student columns) |
| `queue_calls` | Call history: queue ↔ window, called/finished times |

### Seeded services & windows (dump)

**Services**

| ID | Name | Prefix |
|----|------|--------|
| 1 | Cashier | C |
| 2 | Promissory Notes | P |
| 3 | Data Management Office | D |
| 4 | Registrar | R |

**Windows**

| ID | Window | Group | Service |
|----|--------|-------|---------|
| 1 | Cashier 1 | Window 1 | Cashier |
| 2 | Cashier 2 | Window 1 | Cashier |
| 3 | Cashier 3 | Window 1 | Cashier |
| 4 | Promissory Notes | Window 2 | Promissory Notes |
| 5 | DMO | Window 3 | DMO |
| 6 | Registrar | Window 4 | Registrar |

Ticket example: Cashier → `C001`, `C002`, …

> Promissory Notes is **hidden on the kiosk** for now, but the service/window still exist for staff/display if needed.

Domain migrations (if aligning an existing DB instead of full import) live under `database/migrations/2026_04_18_*.php`. Details: [AGENTS.md](AGENTS.md).

---

## Day-to-day operation

### Start with Windows

1. Run `bats/install-pecit-startup.bat` once (creates a Startup shortcut).
2. On each login, `bats/start-pecit-on-windows.bat` starts XAMPP Apache/MySQL, waits for the site, then launches the kiosk.
3. To also open the display automatically, edit `bats/start-pecit-on-windows.bat` and set `START_DISPLAY=1`.
4. To remove autostart, delete the shortcut in the Windows Startup folder.

See `bats/GUIDE.txt` for every launcher’s purpose.

### Kiosk + silent print (XP-58(XP-Q90EC) / 80mm)

1. Run `bats/start-kiosk-chrome.bat` (or `bats/start-kiosk-edge.bat`) — it sets **XP-58(XP-Q90EC)** as the Windows default printer, then opens fullscreen kiosk + silent print.
2. If the printer name differs, edit `PRINTER_EXACT` / `PRINTER_MATCH` in the `.bat`.
3. Customers use the 3-step flow; tickets print without a dialog when launched this way.
4. Close all Chrome/Edge windows first if silent print still shows a dialog.

Edit the URL inside the `.bat` if your local path is different.

### Public display

Open `/display` on the lobby TV/PC (Chrome/Edge recommended for voice).

### Staff

1. Log in with a staff account → `/window`.
2. Use **Call Next**, **Recall**, **Complete** (or **Ctrl + Alt + Space** for Call Next).
3. Optional always-on-top panel: click **Open System Float**, or run `bats/start-staff-float.bat` (~260×220).

### Admin

Log in as admin → `/admin` for dashboard, users, history, and reports.

---

## Developer notes

```bash
composer install
npm install
npm run build
```

- Kiosk views: `resources/views/kiosk/*` + `layouts/app.blade.php` (**locked UI**)
- Staff/admin/login theme: `layouts/panel.blade.php`, `resources/css/panel.css`
- After CSS/JS changes: `npm run build`

For AI agents and deeper technical context, see **[AGENTS.md](AGENTS.md)**.
