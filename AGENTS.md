# Agent guide — PECIT Queuing System

**Audience:** AI coding agents and contributors who need project context before changing code.

This is a **standalone kiosk queue system** for **Philippine Electronics and Communication Institute of Technology Inc. (PECIT)** — Laravel 12 + Blade + Vite/Tailwind + MySQL/MariaDB (usually XAMPP on Windows).

Read **[README.md](README.md)** for operator-oriented setup. This file is the **technical context map** agents should follow.

---

## System purpose (mental model)

```text
Customer → Kiosk (anonymous ticket + 58mm print)
                ↓
         queues / daily_queue_counters
                ↓
Staff window ←→ Call Next / Recall / Complete → queue_calls
                ↓
         Public display (+ optional TTS)
                ↓
Admin: users, history, reports
```

- **No student name/ID** on tickets or in queue creation.
- **Print-only** kiosk (Eco Mode removed).
- **One staff user per window** (app + DB unique when migration applied).

---

## Modules & behavior

### Kiosk (public) — UI LOCKED

- Flow: service → priority (`regular` \| `priority`) → confirm → `KioskController@store` → `kiosk.printing` (auto `window.print()` + countdown).
- Promissory Notes **hidden** from kiosk service list (filter in `KioskController@index`); may still exist for staff/display.
- **Do not redesign/restyle/restructure** kiosk Blade/CSS/JS unless the user explicitly asks (print sizing is an allowed exception when requested).
- Shell: `layouts/app.blade.php`. Views: `resources/views/kiosk/*`.
- Thermal layout: **58mm**, centered number — `kiosk/printing.blade.php`.
- Silent print: not possible from a normal tab; use `start-kiosk-chrome.bat` (`--kiosk --kiosk-printing`). Close all Chrome first.

### Display (public)

- Standalone Blade (not panel layout): `display/index.blade.php`.
- Grouped by `windows.group_name`; polls `GET /display/data`.
- Voice: `speechSynthesis` (browser may require a gesture).

### Staff (auth, `staff` middleware)

- Dashboard: `GET /window` — Call Next, Recall, Complete; waiting list (10); poll `GET /window/state`.
- Shortcut: **Ctrl + Alt + Space** → Call Next.
- History: `GET /window/history` (own served tickets; no edit/delete).
- **System float (Windows always-on-top):**
  - UI: `GET /window/float` → `staff/float.blade.php`
  - Launch: `POST /window/launch-float` (from **Open System Float**) or `start-staff-float.bat` → `tools/staff-float/Start-StaffFloat.ps1`
  - Chrome `--app` window sized ~**260×220**, TopMost, launcher exits after pin
  - **No in-browser float mode** (removed on purpose)
  - `launchFloat` uses Windows `cmd start` on the `.bat`; works best when Apache runs as the interactive desktop user

### Admin (auth, `admin` middleware)

- Dashboard, users CRUD, served history (edit/delete), all tickets, reports.
- Theme: PECIT panel (`layouts/panel`, `panel.css`, sidebars in `partials/`).

### Login

- `auth/login.blade.php` extends `layouts.panel`.
- Passwords: bcrypt (`$2y$…`) **or** legacy plain-text compare in `LoginController` (dump admin uses plain `admin`).

---

## Tech stack

| Layer | Detail |
|-------|--------|
| PHP | 8.2+ |
| Framework | Laravel 12 |
| UI | Blade + Tailwind via Vite |
| CSS entry | `resources/css/app.css` imports `panel.css` |
| DB | MySQL/MariaDB — database name `queuing_system` |
| Dump | `queuing_system.sql` (canonical domain baseline) |

After CSS/JS changes: `npm run build` (output in `public/build/`).

---

## Important paths

| Area | Files |
|------|--------|
| Routes | `routes/web.php` only (no `api.php`) |
| Kiosk (protected UI) | `KioskController`, `views/kiosk/*`, `layouts/app.blade.php` |
| Print 58mm | `views/kiosk/printing.blade.php` |
| Display | `DisplayController`, `views/display/index.blade.php` |
| Staff | `WindowController`, `views/staff/window.blade.php`, `history.blade.php`, `float.blade.php` |
| Float tools | `start-staff-float.bat`, `tools/staff-float/Start-StaffFloat.ps1` |
| Kiosk silent print | `start-kiosk-chrome.bat` |
| Admin | `AdminDashboardController`, `HistoryController`, `UserManagementController`, `views/admin/*` |
| Panel theme | `layouts/panel.blade.php`, `css/panel.css`, `partials/admin-sidebar.blade.php`, `partials/staff-sidebar.blade.php` |
| Queue logic | `app/Services/QueueService.php` |
| Security headers | `SetSecurityHeaders` middleware |
| Logo | `public/logo/logo.png` |

---

## Routes (summary)

| Method | Path | Name / notes |
|--------|------|----------------|
| GET | `/` | Redirect → kiosk |
| GET/POST | `/login`, POST `/logout` | Auth |
| GET/POST | `/kiosk` | Public ticket |
| GET | `/display`, `/display/data` | Public display |
| GET | `/admin` | Admin dashboard |
| GET | `/admin/queues/waiting` | Live waiting JSON |
| resource | `/admin/users` | User CRUD |
| GET/PUT/DELETE | `/admin/history…` | History, tickets, reports, edit |
| GET | `/window` | Staff dashboard |
| GET | `/window/float` | Float UI |
| POST | `/window/launch-float` | Start `.bat` on server PC |
| GET | `/window/state` | Staff poll JSON |
| POST | `/window/call-next`, `/recall`, `/complete` | Actions (`back()`) |
| GET | `/window/history` | Staff history |

---

## Database context

### Engine & dump

- Prefer **MySQL** with `DB_DATABASE=queuing_system`.
- Import **`queuing_system.sql`** for baseline schema + seed data.
- Dump may **not** include Laravel `sessions` / `cache` / `jobs` — for local XAMPP, prefer `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` unless those tables exist.
- Stock `php artisan migrate` on a dump-created DB can fail (“table already exists”). Prefer dump + selective `--path` domain migrations.

### Domain migrations

| Migration | Purpose |
|-----------|---------|
| `2026_04_18_000000_add_group_name_to_windows_table.php` | `windows.group_name` |
| `2026_04_18_010000_drop_student_columns_from_queues_table.php` | Drop student columns |
| `2026_04_18_020000_configure_windows_services_and_staff_constraints.php` | Seed + `users.window_id` unique |
| `2026_04_18_030000_reconfigure_window_layout.php` | Window layout reconfiguration |

Rebuild dump helper: `php database/sql/build_queuing_system_dump.php`.

### Tables

| Table | Role |
|-------|------|
| `services` | `service_name`, `prefix` (ticket code) |
| `windows` | Counter; `service_id`, `group_name`, `status` |
| `users` | `role` admin\|staff; staff `window_id` |
| `daily_queue_counters` | Locked increment per service + date in kiosk store |
| `queues` | `queue_number`, `service_id`, `priority` 0/1, `status` waiting\|serving\|done\|cancelled, `queue_date` — **no student columns** |
| `queue_calls` | `queue_id`, `window_id`, `called_time`, `finished_time` (null = open). Display recall token derived in app (`id` + `called_time`) |

### Seeded reference data (dump)

**Services:** Cashier `C`, Promissory Notes `P`, DMO `D`, Registrar `R`.

**Windows:** Cashier 1–3 → group Window 1; Promissory Notes → Window 2; DMO → Window 3; Registrar → Window 4.

**Users in dump:**

| Email | Role | window_id | Password note |
|-------|------|-----------|---------------|
| `admin@gmail.com` | admin | null | Plain `admin` in dump; `LoginController` accepts plain or bcrypt |
| `rhem@gmail.com` | staff | 1 (Cashier 1) | Bcrypt in dump |
| `omar@gmail.com` | staff | 2 (Cashier 2) | Bcrypt in dump |

Models: check `$timestamps` / `$fillable` per model — several domain models use `$timestamps = false`.

---

## Business rules agents must respect

1. Anonymous kiosk — no student fields in UI or create path; `Queue` `$fillable` excludes them.
2. Print-only — no Eco Mode / `output_mode`.
3. Priority — only `regular` \| `priority`; DB `priority` = 0/1.
4. Recall/TTS — keep call-token (or equivalent) change behavior for re-announce.
5. Issued time — pass `$issuedAt` to views; don’t rely on Eloquent `created_at` for kiosk.
6. One staff per window — validate in `UserManagementController` + unique index when present.
7. Avoid Laravel `Cache` for kiosk/display unless cache store is known-good.
8. **Kiosk design lock** — theme work → login/staff/admin (`panel`), not kiosk.
9. Don’t change DB schema / queue generation logic for pure UI tasks unless asked.
10. System float — keep Windows launcher path; don’t revive browser-only float unless asked.

---

## UI / offline

- Local assets only (`@vite`, `public/`) — no CDN dependency for kiosk/offline PCs.
- CSP via `SetSecurityHeaders` — keep inline scripts compatible or update CSP.
- Panel styles are `.pecit-*`; kiosk uses its own inline CSS under `layouts.app`.

---

## Commands

```bash
composer install
npm install
npm run build
php artisan serve
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

Windows helpers:

```bat
start-kiosk-chrome.bat
start-staff-float.bat
```

---

## Conventions for agents

1. Change only files needed for the task.
2. Kiosk: keep `KioskController` validation in sync with the step UI; treat kiosk views as protected.
3. Staff/admin/login: extend `layouts.panel`; reuse `pecit-*` classes.
4. Align `queuing_system.sql` if you intentionally change domain schema.
5. Never commit `.env` or real secrets.
6. When behavior/launchers/schema/users change, update **README.md** (operators) and **this file** (agents).

---

## Smoke checks

- [ ] Kiosk 3-step + print (UI unchanged unless print task)
- [ ] Kiosk POST validation (`service_id`, `priority`)
- [ ] 58mm thermal; optional silent print via `start-kiosk-chrome.bat`
- [ ] Display + `/display/data`; voice if enabled
- [ ] Staff call-next / recall / complete; waiting list; Ctrl+Alt+Space
- [ ] Open System Float / bat → always-on-top ~260×220; launcher exits
- [ ] Admin dashboard/users/history/reports (PECIT theme)
- [ ] Auth middleware: admin vs staff

---

## When requirements conflict

State the tradeoff clearly (e.g. silent print needs Chrome kiosk flags; always-on-top needs a separate Windows window). Prefer **documented, testable** behavior on the real deployment browser/PC.
