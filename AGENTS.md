# Agent guide — PECIT Queuing System

**Audience:** AI coding agents and contributors who need project context before changing code.

This is a **standalone kiosk queue system** for **Philippine Electronics and Communication Institute of Technology Inc. (PECIT)** — Laravel 12 + Blade + Vite/Tailwind + MySQL/MariaDB (usually XAMPP on Windows). Capstone research project (**v4** feature set: fair scheduling, multi-counter ETA, admin reports, secret About page).

Read **[README.md](README.md)** for operator-oriented setup. This file is the **technical context map** agents should follow.

---

## System purpose (mental model)

```text
Customer → Kiosk (anonymous ticket + ETA confirm + 80mm print)
                ↓
         queues / daily_queue_counters
                ↓
Staff windows (shared service queue, 2P→1R) → queue_calls
                ↓
         Public display (+ optional TTS)
                ↓
Admin: live dashboard, users, history, wait/service reports
```

- **No student name/ID** on tickets or in queue creation.
- **Print-only** kiosk (Eco Mode removed).
- **One staff user per window** (app + DB unique when migration applied).
- **Multi-cashier compatible:** Cashier 1–3 share one Cashier waiting queue; each has an independent open call + timer.

---

## Modules & behavior

### Kiosk (public) — UI LOCKED

- Flow: service → priority (`regular` \| `priority`) → confirm → `KioskController@store` → `kiosk.printing` (auto `window.print()` + countdown).
- Confirm step may show live waiting counts + estimated wait (`GET /kiosk/estimate`); do not redesign the step chrome.
- Thermal ticket may add **one** compact line when ETA is available: `Estimated Time: N minutes` (omit when history is insufficient).
- Print sizing (current baseline): title/lines ~13px, number ~34px, footer ~12px — change only when asked.
- Promissory Notes **hidden** from kiosk service list (filter in `KioskController@index`); may still exist for staff/display.
- **Do not redesign/restyle/restructure** kiosk Blade/CSS/JS unless the user explicitly asks (print sizing / ETA line are allowed exceptions when requested).
- Shell: `layouts/app.blade.php`. Views: `resources/views/kiosk/*`.
- Thermal layout: **80mm** (XP-58(XP-Q90EC)), centered number — `kiosk/printing.blade.php`.
- Silent print: not possible from a normal tab; use `bats/start-kiosk-chrome.bat` (`--kiosk --kiosk-printing`). Launcher matches printer **XP-Q90EC**, waits 10s, clears Chrome sticky printer; close all Chrome first.
- New tickets should set `queues.created_at` when the column exists (used for average waiting time in reports).

### Staff (auth, `staff` middleware)

- Dashboard: `GET /window` — Call Next, Recall, Complete; waiting list (10, fair call order); poll `GET /window/state` (includes `serving_started_at`).
- Per-window **service timer** from open `queue_calls.called_time` (independent across Cashier 1 / Cashier 2 / …).
- Shortcut: **Ctrl + Alt + Space** → Call Next.
- History: `GET /window/history` (own served tickets; no edit/delete).
- **Fair scheduling (service-wide):** 2 Priority → 1 Regular across all windows sharing a `service_id`. `callNext` locks the `services` row then claims via `FairQueueScheduler` (atomic status update; no duplicate ticket assignment).
- **System float (Windows always-on-top):**
  - UI: `GET /window/float` → `staff/float.blade.php` (also shows compact timer)
  - Launch: `POST /window/launch-float` (from **Open System Float**) or `bats/start-staff-float.bat` → `tools/staff-float/Start-StaffFloat.ps1`
  - Chrome `--app` window sized ~**260×220**, TopMost, launcher exits after pin
  - **No in-browser float mode** (removed on purpose)
  - `launchFloat` uses Windows `cmd start` on the `.bat`; works best when Apache runs as the interactive desktop user

### Queue intelligence services

| Class | Role |
|-------|------|
| `App\Services\FairQueueScheduler` | Shared 2P→1R order; claim/peek; tickets-ahead simulation |
| `App\Services\WaitTimeEstimator` | Multi-counter ETA from history + waiting + serving |
| `App\Services\QueueService` | Latest call helpers for a window |

### Display (public)

- Standalone Blade (not panel layout): `display/index.blade.php`.
- Grouped by `windows.group_name`; polls `GET /display/data`.
- Waiting columns use **same fair call order** as Call Next (`FairQueueScheduler::orderedWaiting`), max 10 rows.
- Compact table CSS so 10 rows fit without scrolling; keep waiting section position under Now Serving (do not pull it upward casually).
- Voice: `speechSynthesis` (preferred voice may be set in page JS; browser may require a gesture).

### Admin (auth, `admin` middleware)

- Dashboard: live totals + waiting table (polls `/admin/queues/waiting` with counts + priority); quick links; SVG icons.
- Users CRUD, served history (edit/delete), all tickets.
- **Reports:** overall avg waiting time + avg service time; **Averages by Window** table (served count, avg wait, avg service).
  - Wait ≈ `queues.created_at` → `queue_calls.called_time`
  - Service ≈ `called_time` → `finished_time`
- Theme: PECIT panel (`layouts/panel`, `panel.css`, sidebars in `partials/` with local SVG icons via `partials/icon.blade.php`).

### Login

- `auth/login.blade.php` extends `layouts.panel`.
- Passwords: bcrypt (`$2y$…`) **or** legacy plain-text compare in `LoginController` (dump admin uses plain `admin`).

### Secret About / capstone credits

- Hotkey: **Ctrl + Alt + Shift + A** (`partials/secret-about-hotkey.blade.php` included from panel, app, display, float, about).
- Route: `GET /about` → `AboutController` + `views/about/index.blade.php` (not in sidebars).
- Roster: `config/about.php` — **5 members** (name, role, focus, photo filename).
- Photos: **private hidden folder** `storage/app/private/about/team/` (Windows Hidden attribute; **not** under `public/`).
- Stream only via `GET /about/photo/{file}` for allowlisted filenames; missing photo → initials avatar.

---

## Tech stack

| Layer | Detail |
|-------|--------|
| PHP | 8.2+ |
| Framework | Laravel 12 |
| UI | Blade + Tailwind via Vite |
| CSS entry | `resources/css/app.css` imports bundled Source Sans 3 + `panel.css` |
| DB | MySQL/MariaDB — database name `queuing_system` |
| Dump | `queuing_system.sql` (canonical domain baseline) |

After CSS/JS/font changes: `npm run build` (output in `public/build/`).

---

## Important paths

| Area | Files |
|------|--------|
| Routes | `routes/web.php` only (no `api.php`) |
| Kiosk (protected UI) | `KioskController`, `views/kiosk/*`, `layouts/app.blade.php` |
| Print 80mm (XP-58(XP-Q90EC)) | `views/kiosk/printing.blade.php` |
| Display | `DisplayController`, `views/display/index.blade.php` |
| Staff | `WindowController`, `views/staff/window.blade.php`, `history.blade.php`, `float.blade.php` |
| Float tools | `bats/start-staff-float.bat`, `tools/staff-float/Start-StaffFloat.ps1` |
| Kiosk silent print | `bats/start-kiosk-chrome.bat`, `tools/kiosk/*.ps1` |
| Launcher guide | `bats/GUIDE.txt` |
| Admin | `AdminDashboardController`, `HistoryController`, `UserManagementController`, `views/admin/*` |
| Panel theme | `layouts/panel.blade.php`, `css/panel.css`, `partials/admin-sidebar.blade.php`, `partials/staff-sidebar.blade.php`, `partials/icon.blade.php` |
| Queue logic | `FairQueueScheduler`, `WaitTimeEstimator`, `QueueService` |
| Capstone About | `AboutController`, `config/about.php`, `views/about/*`, `storage/app/private/about/team/` |
| Diagrams | `README.md` (Mermaid ERD + flowchart), `docs/erd.md`, `docs/system-flowchart.md` |
| Security headers | `SetSecurityHeaders` middleware |
| Logo | `public/logo/logo.png` |

---

## Routes (summary)

| Method | Path | Name / notes |
|--------|------|----------------|
| GET | `/` | Redirect → kiosk |
| GET/POST | `/login`, POST `/logout` | Auth |
| GET/POST | `/kiosk` | Public ticket |
| GET | `/kiosk/estimate` | Waiting counts + ETA JSON |
| GET | `/about` | Secret capstone About (Ctrl+Alt+Shift+A) |
| GET | `/about/photo/{file}` | Private team photo stream (allowlisted only) |
| GET | `/display`, `/display/data` | Public display |
| GET | `/admin` | Admin dashboard |
| GET | `/admin/queues/waiting` | Live waiting JSON (+ counts) |
| resource | `/admin/users` | User CRUD |
| GET/PUT/DELETE | `/admin/history…` | History, tickets, reports, edit |
| GET | `/window` | Staff dashboard |
| GET | `/window/float` | Float UI |
| POST | `/window/launch-float` | Start `.bat` on server PC |
| GET | `/window/state` | Staff poll JSON (`serving_started_at`) |
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
| `services` | `service_name`, `prefix` (ticket code) — also used as Call Next mutex via `lockForUpdate` |
| `windows` | Counter; `service_id`, `group_name`, `status` |
| `users` | `role` admin\|staff; staff `window_id` |
| `daily_queue_counters` | Locked increment per service + date in kiosk store |
| `queues` | `queue_number`, `service_id`, `priority` 0/1, `status` waiting\|serving\|done\|cancelled, `queue_date`, optional `created_at` for wait metrics — **no student columns** |
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
3. Priority — only `regular` \| `priority`; DB `priority` = 0/1. Call order is **2 Priority → 1 Regular** per service (shared across multi-window services like Cashier).
4. Recall/TTS — keep call-token (or equivalent) change behavior for re-announce.
5. Issued time — pass `$issuedAt` to views; don’t rely on Eloquent `created_at` for kiosk display time (still set DB `created_at` when present for wait averages).
6. One staff per window — validate in `UserManagementController` + unique index when present.
7. Avoid Laravel `Cache` for kiosk/display unless cache store is known-good.
8. **Kiosk design lock** — theme work → login/staff/admin (`panel`), not kiosk. Confirm ETA block + single thermal ETA line are intentional exceptions.
9. Don’t change DB schema / queue generation logic for pure UI tasks unless asked.
10. System float — keep Windows launcher path; don’t revive browser-only float unless asked.
11. Multi-counter ETA — divide by active cashiers/windows for the service; omit print ETA when historical completes are insufficient (< 3).
12. `callNext` / `complete` must stay window-scoped for timers and must not complete another counter’s open call.
13. Do not put team photos under `public/`; keep `storage/app/private/about/team/` and allowlisted photo streaming.
14. Do not add the About page to sidebars; keep Ctrl+Alt+Shift+A as the entry.

---

## UI / offline

- Local assets only (`@vite`, `public/`) — no CDN dependency for kiosk/staff/admin/offline PCs.
- Icons: inline SVG via `resources/views/partials/icon.blade.php` (no Font Awesome / icon CDN).
- Fonts: bundled **Source Sans 3** (`@fontsource-variable/source-sans-3`) into `public/build/` via `npm run build`.
- Optional docs tool: `public/flowchart-viewer.html` uses local `public/vendor/mermaid/mermaid.min.js` (not a CDN).
- CSP via `SetSecurityHeaders` — keep inline scripts compatible or update CSP (`script-src`/`style-src` allow `'unsafe-inline'` for Blade).
- Panel styles are `.pecit-*`; kiosk uses its own inline CSS under `layouts.app`.
- After CSS/font/icon changes: `npm run build`.

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

Windows helpers (see `bats/GUIDE.txt`):

```bat
bats\install-pecit-startup.bat
bats\start-pecit-on-windows.bat
bats\start-kiosk-chrome.bat
bats\start-staff-float.bat
```

Kiosk launchers wait **10 seconds** before opening the browser (gives Apache/printer time after boot).

---

## Conventions for agents

1. Change only files needed for the task.
2. Kiosk: keep `KioskController` validation in sync with the step UI; treat kiosk views as protected.
3. Staff/admin/login: extend `layouts.panel`; reuse `pecit-*` classes and `partials/icon`.
4. Align `queuing_system.sql` if you intentionally change domain schema.
5. Never commit `.env` or real secrets; avoid committing large personal photos unless the team wants them in-repo.
6. When behavior/launchers/schema/users change, update **README.md** (operators) and **this file** (agents).

---

## Smoke checks

- [ ] Kiosk 3-step + print (UI unchanged unless print/ETA task)
- [ ] Kiosk confirm shows waiting counts + ETA via `/kiosk/estimate`
- [ ] Kiosk POST validation (`service_id`, `priority`)
- [ ] 80mm thermal (XP-58(XP-Q90EC)); optional `Estimated Time: N minutes` line only; fonts readable
- [ ] Silent print via `bats/start-kiosk-chrome.bat` (survives countdown; XP-Q90EC match)
- [ ] Display + `/display/data`; waiting list = fair call order; 10 rows no scroll; voice if enabled
- [ ] Staff call-next / recall / complete; fair 2P→1R; independent service timers
- [ ] Concurrent Call Next on Cashier 1 + Cashier 2 never duplicates a ticket
- [ ] Open System Float / bat → always-on-top ~260×220; launcher exits
- [ ] Admin dashboard live counts; reports show overall + per-window avg wait/service
- [ ] Offline: no CDN fonts/icons; `npm run build` assets load
- [ ] Ctrl+Alt+Shift+A opens About; photos from private folder when present
- [ ] Auth middleware: admin vs staff

---

## When requirements conflict

State the tradeoff clearly (e.g. silent print needs Chrome kiosk flags; always-on-top needs a separate Windows window; ETA needs enough completed history). Prefer **documented, testable** behavior on the real deployment browser/PC.
