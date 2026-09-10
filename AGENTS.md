# Agent guide — PECIT Queuing System

**Audience:** AI coding agents and contributors who need project context before changing code.

This is a **standalone kiosk queue system** for **Philippine Electronics and Communication Institute of Technology Inc. (PECIT)** — Laravel 12 + Blade + Vite/Tailwind + MySQL/MariaDB (usually XAMPP on Windows). Capstone research project (**v4** feature set: fair scheduling, multi-counter ETA, admin reports, secret About page).

Read **[README.md](README.md)** for operator-oriented setup. This file is the **technical context map** agents should follow.

---

## System purpose (mental model)

```text
Customer → Kiosk (student ID on confirm + ETA + 80mm print, no name on ticket)
         ↘ Walk-in: corner PIN (`settings.walkin_pin`) — no Guard login
                ↓
         queues / daily_queue_counters / students / settings
                ↓
Staff windows (shared 2P→1R, Hold/set-aside, student name while serving) → queue_calls
                ↓
         Public display (numbers only, no names, no held)
                ↓
Admin: users, students, kiosk PIN, walk-in issue, wait/service reports (done only)
```

- **Student ID on confirm only** — name is never printed or shown on the public display.
- **Print-only** kiosk (Eco Mode removed).
- **One staff user per window** (app + DB unique when migration applied).
- **Multi-cashier compatible:** Cashier 1–3 share one Cashier waiting queue; each has an independent open call + timer.
- **One open ticket per student per day** (`waiting` \| `serving` \| `held`, any service). Walk-in tickets (no student ID) are issued with the **kiosk PIN** (or admin `/guard`) and may be many at once. Guard role **cannot log in**.

---

## Modules & behavior

### Kiosk (public) — UI LOCKED

- Flow: service → priority (`regular` \| `priority`) → confirm (Student ID keypad + ETA) → `KioskController@store` → `kiosk.printing` (auto `window.print()` + countdown). Still **3 steps**.
- Confirm step may show live waiting counts + estimated wait (`GET /kiosk/estimate`) and Student ID lookup (`GET /kiosk/student`); do not redesign the step chrome.
- Confirm stays disabled until lookup succeeds. Unknown ID or an already-open ticket today is an error. Name may appear on confirm only so the student can check it; **never print the name**.
- Confirm uses a compact two-column layout (summary + keypad) so a **1024×600** kiosk screen does not need to scroll.
- Thermal ticket may add **one** compact line when ETA is available: `Estimated Time: N minutes` (omit when history is insufficient).
- Print sizing (current baseline): title/lines ~13px, number ~34px, footer ~12px — change only when asked.
- Promissory Notes **hidden** from kiosk service list (filter in `KioskController@index`); may still exist for staff/display.
- **Do not redesign/restyle/restructure** kiosk Blade/CSS/JS unless the user explicitly asks (print sizing / ETA line / confirm Student ID keypad / tiny walk-in PIN corner button are allowed exceptions when requested).
- Tiny bottom-right hit target opens a PIN pad. PIN is stored in `settings.walkin_pin` (Admin → Kiosk PIN), not `.env`. Correct PIN shows a walk-in issue overlay (no Student ID). PIN is checked on the server only; unlock lasts a few minutes. Print still number-only; countdown returns to the kiosk. This is not a student multi-ticket ID.
- Shell: `layouts/app.blade.php`. Views: `resources/views/kiosk/*`.
- Thermal layout: **80mm** (XP-58(XP-Q90EC)), centered number — `kiosk/printing.blade.php`.
- Silent print: not possible from a normal tab; use `bats/start-kiosk-chrome.bat` (`--kiosk --kiosk-printing`). Launcher matches printer **XP-Q90EC**, waits 10s, clears Chrome sticky printer; close all Chrome first.
- New tickets should set `queues.created_at` when the column exists (used for average waiting time in reports).

### Guard (kiosk PIN only — no login)

- Guards **cannot log in**. Walk-ins are issued at the kiosk: tiny corner button → PIN → overlay.
- PIN lives in `settings` (`walkin_pin`), edited at `GET/PUT /admin/settings`. Default seed `1981`.
- Tickets have `student_id` null, `issued_by` = kiosk issuer user id (dump `guard@gmail.com`, no login), `issue_reason` set. Many open walk-in tickets are allowed.
- Promissory Notes hidden from the issue list (same as kiosk).
- Admin may still issue from `GET/POST /guard` (admin middleware). Staff serving label is **Walk-in** (not printed, not on the public display).

### Staff (auth, `staff` middleware)

- Dashboard: `GET /window` — Call Next, Recall, Complete, Hold; waiting list (10, fair call order); held list with Call; poll `GET /window/state` (includes `serving_started_at`, `current_name`, `held_list`).
- Per-window **service timer** from open `queue_calls.called_time` (independent across Cashier 1 / Cashier 2 / …).
- Staff sees **student name** while serving (and on held rows), or **Walk-in** for guard-issued tickets. Public display does not.
- **Hold** (`POST /window/hold`): finish the open call, set `queues.status = held`, free the window. Held tickets leave Now Serving and waiting lists.
- **Call held** (`POST /window/call-held`): resume a held ticket at this window without 2P→1R; requires no open serving ticket.
- Shortcuts: **Alt+N** → Call Next; **Alt+R** → Recall; **Alt+C** → Complete.
- History: `GET /window/history` (own **done** tickets only; hold sessions are not counted as served).
- **Fair scheduling (service-wide):** 2 Priority → 1 Regular across all windows sharing a `service_id`. `callNext` locks the `services` row then claims via `FairQueueScheduler` (atomic status update; no duplicate ticket assignment).
- **System float (Windows always-on-top):**
  - UI: `GET /window/float` → `staff/float.blade.php` (also shows compact timer)
  - Launch: `POST /window/launch-float` (from **Open System Float**) or `bats/start-staff-float.bat` → `tools/staff-float/Start-StaffFloat.ps1`
  - Chrome `--app` window sized ~**260×270**, TopMost, launcher exits after pin
  - **No in-browser float mode** (removed on purpose)
  - `launchFloat` uses Windows `cmd start` on the `.bat`; works best when Apache runs as the interactive desktop user

### Queue intelligence services

| Class | Role |
|-------|------|
| `App\Services\FairQueueScheduler` | Shared 2P→1R order; claim/peek; tickets-ahead simulation |
| `App\Services\WaitTimeEstimator` | Multi-counter ETA from history + waiting + serving |
| `App\Services\QueueService` | Latest call helpers; serving-only now-serving; staff-only student name |
| `App\Services\StudentQueueGuard` | Normalize ID; lookup; one open ticket today |
| `App\Services\KioskWalkInGate` | Kiosk PIN unlock for walk-in issue (session, not a login) |

### Display (public)

- Standalone Blade (not panel layout): `display/index.blade.php`.
- Grouped by `windows.group_name`; polls `GET /display/data`.
- Waiting columns use **same fair call order** as Call Next (`FairQueueScheduler::orderedWaiting`), max 10 rows. **Held tickets are omitted.**
- Now Serving uses open calls whose queue is still `serving` (Hold clears the number).
- Compact table CSS so 10 rows fit without scrolling; keep waiting section position under Now Serving (do not pull it upward casually).
- Voice: `speechSynthesis` (preferred voice may be set in page JS; browser may require a gesture).
- **No student names** on the display.

### Admin (auth, `admin` middleware)

- Dashboard: live totals + waiting table (polls `/admin/queues/waiting` with counts + priority); quick links; SVG icons.
- Users CRUD, **students** (add / delete / CSV import), served history (edit/delete), all tickets (includes `held` + student name for admin).
- **Reports:** overall avg waiting time + avg service time; **Averages by Window** table (served count, avg wait, avg service). Count **done** tickets only (latest completed call; hold sessions excluded).
  - Wait ≈ `queues.created_at` → `queue_calls.called_time`
  - Service ≈ `called_time` → `finished_time`
- Theme: PECIT panel (`layouts/panel`, `panel.css`, sidebars in `partials/` with local SVG icons via `partials/icon.blade.php`).

### Login

- `auth/login.blade.php` extends `layouts.panel`.
- Passwords: bcrypt (`$2y$…`) **or** legacy plain-text compare in `LoginController` (dump admin uses plain `admin`).
- `guard` role **cannot log in**; walk-ins use the kiosk PIN.

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
| Walk-in | `KioskWalkInGate`, kiosk PIN overlay; admin `/guard` → `GuardIssueController`, `views/guard/issue.blade.php` |
| Kiosk PIN | `SettingsController`, `views/admin/settings.blade.php`, `settings` table |
| Admin | `AdminDashboardController`, `HistoryController`, `UserManagementController`, `StudentManagementController`, `SettingsController`, `views/admin/*` |
| Panel theme | `layouts/panel.blade.php`, `css/panel.css`, `partials/admin-sidebar.blade.php`, `partials/staff-sidebar.blade.php`, `partials/icon.blade.php` |
| Queue logic | `FairQueueScheduler`, `WaitTimeEstimator`, `QueueService`, `StudentQueueGuard`, `TicketIssuer`, `KioskWalkInGate` |
| Capstone About | `AboutController`, `config/about.php`, `views/about/*`, `storage/app/private/about/team/` |
| Diagrams | `README.md` (Mermaid ERD + flowchart), `docs/erd.md`, `docs/erd/queuing_system.erd` (ERD Designer / MariaDB; do not hand-edit), `docs/system-flowchart.md`, Archify maps `docs/archify/pecit-runtime.architecture.html` and `docs/archify/pecit-erd.architecture.html` |
| Security headers | `SetSecurityHeaders` middleware |
| Logo | `public/logo/logo.png` |

---

## Routes (summary)

| Method | Path | Name / notes |
|--------|------|----------------|
| GET | `/` | Redirect → kiosk |
| GET/POST | `/login`, POST `/logout` | Auth |
| GET/POST | `/kiosk` | Public ticket (`student_id` required on POST) |
| GET | `/kiosk/estimate` | Waiting counts + ETA JSON |
| GET | `/kiosk/student` | Confirm-step student ID lookup |
| GET | `/kiosk/walk-in/status` | PIN session unlocked? |
| POST | `/kiosk/walk-in/unlock` | Check walk-in PIN |
| POST | `/kiosk/walk-in` | Issue walk-in after PIN unlock |
| GET | `/about` | Secret capstone About (Ctrl+Alt+Shift+A) |
| GET | `/about/photo/{file}` | Private team photo stream (allowlisted only) |
| GET | `/display`, `/display/data` | Public display |
| GET | `/admin` | Admin dashboard |
| GET | `/admin/queues/waiting` | Live waiting JSON (+ counts) |
| resource | `/admin/users` | User CRUD |
| GET/POST/DELETE | `/admin/students…` | Student allowlist (add, delete, CSV import) |
| GET/PUT/DELETE | `/admin/history…` | History, tickets, reports, edit |
| GET/PUT | `/admin/settings` | Kiosk walk-in PIN (database) |
| GET/POST | `/guard` | Admin-only walk-in issue |
| GET | `/window` | Staff dashboard |
| GET | `/window/float` | Float UI |
| POST | `/window/launch-float` | Start `.bat` on server PC |
| GET | `/window/state` | Staff poll JSON (`serving_started_at`, `current_name`, `held_list`) |
| POST | `/window/call-next`, `/recall`, `/complete`, `/hold`, `/call-held` | Actions (`back()`) |
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
| `2026_09_09_000000_add_students_hold_and_queue_student_id.php` | `students` table, `queues.student_id`, status `held`, demo IDs |
| `2026_09_09_010000_align_students_collation_with_queues.php` | Match `students` collation to dump (`utf8mb4_general_ci`) |
| `2026_09_10_000000_add_guard_role_and_walk_in_tickets.php` | `users.role` + `guard`; `queues.issued_by` / `issue_reason`; demo `guard@gmail.com` |
| `2026_09_10_010000_add_settings_walkin_pin.php` | `settings` table; seed `walkin_pin` = `1981` |

Rebuild dump helper: `php database/sql/build_queuing_system_dump.php`.

### Tables

| Table | Role |
|-------|------|
| `services` | `service_name`, `prefix` (ticket code) — also used as Call Next mutex via `lockForUpdate` |
| `windows` | Counter; `service_id`, `group_name`, `status` |
| `users` | `role` admin\|staff\|guard; staff `window_id`; guard issuer cannot log in |
| `settings` | Key/value (`walkin_pin` for kiosk walk-in) |
| `daily_queue_counters` | Locked increment per service + date in kiosk store |
| `students` | Allowlisted kiosk IDs (`student_id`, `name`) — dump seeds `2024-0001`…`2024-0005` |
| `queues` | `queue_number`, `service_id`, optional `student_id`, optional `issued_by` / `issue_reason` (walk-in), `priority` 0/1, `status` waiting\|serving\|done\|cancelled\|held, `queue_date`, optional `created_at` for wait metrics — **name is never stored on the ticket** |
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
| `guard@gmail.com` | guard | null | Issuer row for `issued_by` only — **cannot log in**; PIN is in `settings` |

Models: check `$timestamps` / `$fillable` per model — several domain models use `$timestamps = false`.

---

## Business rules agents must respect

1. Kiosk collects **Student ID** on confirm only; print and public display stay number-only (no name). `Queue` `$fillable` may include `student_id`.
2. Print-only — no Eco Mode / `output_mode`.
3. Priority — only `regular` \| `priority`; DB `priority` = 0/1. Call order is **2 Priority → 1 Regular** per service (shared across multi-window services like Cashier). **Call held** skips that order.
4. Recall/TTS — keep call-token (or equivalent) change behavior for re-announce.
5. Issued time — pass `$issuedAt` to views; don’t rely on Eloquent `created_at` for kiosk display time (still set DB `created_at` when present for wait averages).
6. One staff per window — validate in `UserManagementController` + unique index when present.
7. Avoid Laravel `Cache` for kiosk/display unless cache store is known-good.
8. **Kiosk design lock** — theme work → login/staff/admin (`panel`), not kiosk. Confirm ETA block, Student ID keypad, and single thermal ETA line are intentional exceptions.
9. Don’t change DB schema / queue generation logic for pure UI tasks unless asked.
10. System float — keep Windows launcher path; don’t revive browser-only float unless asked.
11. Multi-counter ETA — divide by active cashiers/windows for the service; omit print ETA when historical completes are insufficient (< 3). Hold sessions are not used as completed-service samples.
12. `callNext` / `complete` / `hold` must stay window-scoped for timers and must not complete another counter’s open call.
13. Do not put team photos under `public/`; keep `storage/app/private/about/team/` and allowlisted photo streaming.
14. Do not add the About page to sidebars; keep Ctrl+Alt+Shift+A as the entry.
15. One open ticket per student per day (`waiting`/`serving`/`held`). Hold is not Complete; reports count **done** only.
16. Walk-in tickets: kiosk corner PIN (`settings.walkin_pin`, Admin → Kiosk PIN) or admin `/guard`. No Student ID; `issued_by` is the issuer user id. Guard role cannot log in. Do not add a multi-ticket student ID on the public kiosk. Print and display stay number-only.

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
bats\install-mysql-backup-startup.bat
bats\backup-mysql-on-startup.bat
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

- [ ] Kiosk 3-step + print (confirm adds Student ID keypad; rest of chrome unchanged)
- [ ] Kiosk confirm shows waiting counts + ETA via `/kiosk/estimate`
- [ ] Kiosk Student ID lookup via `/kiosk/student`; Confirm disabled until OK
- [ ] Unknown ID and second ticket same day are rejected
- [ ] Kiosk POST validation (`service_id`, `priority`, `student_id`)
- [ ] 80mm thermal (XP-58(XP-Q90EC)); optional `Estimated Time: N minutes` line only; **no student name**
- [ ] Silent print via `bats/start-kiosk-chrome.bat` (survives countdown; XP-Q90EC match)
- [ ] Display + `/display/data`; waiting list = fair call order; 10 rows no scroll; voice if enabled; no names; held absent
- [ ] Staff call-next / recall / complete / hold / call-held; name while serving; fair 2P→1R; independent service timers
- [ ] Concurrent Call Next on Cashier 1 + Cashier 2 never duplicates a ticket
- [ ] Open System Float / bat → always-on-top ~260×270; launcher exits; compact name + Hold
- [ ] Admin dashboard live counts; reports show overall + per-window avg wait/service (**done** only)
- [ ] Offline: no CDN fonts/icons; `npm run build` assets load
- [ ] Ctrl+Alt+Shift+A opens About; photos from private folder when present
- [ ] Admin Students: add, delete, CSV import; kiosk lookup uses the list
- [ ] Guard cannot log in; kiosk corner PIN from `settings.walkin_pin` (Admin editable): unlock → issue walk-in → print number-only → back to kiosk; wrong PIN rejected
- [ ] Auth middleware: admin vs staff

---

## When requirements conflict

State the tradeoff clearly (e.g. silent print needs Chrome kiosk flags; always-on-top needs a separate Windows window; ETA needs enough completed history). Prefer **documented, testable** behavior on the real deployment browser/PC.
