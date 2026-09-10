# Level 1 data flow diagram — queue system

**Context:** Physical **system boundary** = the Laravel web application + MySQL used by kiosk, display, staff, and admin.  
**Level 1** decomposes that system into **four major processes**, four **logical data stores**, and **four external entities**. Walk-in tickets are issued at the kiosk with a PIN (`settings.walkin_pin`); there is no Guard login.

---

## Diagram (Mermaid)

Paste into [mermaid.live](https://mermaid.live) or open `dfd-level1.mmd`. For printing, export **SVG** and use **Landscape** or **Fit to page**.

```mermaid
%%{init: {'flowchart': {'nodeSpacing': 22, 'rankSpacing': 28, 'padding': 5}, 'themeVariables': { 'fontSize': '10px'}}}%%
flowchart TB
    subgraph EXT["External entities"]
        direction LR
        EC[Customer]
        ES[Staff]
        EA[Administrator]
        ED[Display client]
    end

    subgraph SYS["Queue management system — Level 1 processes"]
        direction TB
        P1["1.0 Issue ticket"]
        P2["2.0 Produce display data"]
        P3["3.0 Serve at window"]
        P4["4.0 Administer system"]
    end

    subgraph DS["Data stores"]
        direction LR
        D1[("D1 Queues")]
        D2[("D2 Daily counters")]
        D3[("D3 Queue calls")]
        D4[("D4 Master data")]
    end

    EC -->|Service, priority, Student ID or walk-in PIN| P1
    P1 -->|Queue number, ETA| EC

    ED -->|Poll /display, /display/data| P2
    P2 -->|Now serving, waiting list| ED

    ES -->|Call next, recall, complete, hold| P3
    P3 -->|Current ticket, name, timer| ES

    EA -->|Users, students, kiosk PIN, reports| P4
    P4 -->|Screens, CSV result| EA

    D4 -->|Valid services + student allowlist + walk-in PIN| P1
    P1 -->|New ticket row| D1
    P1 -->|Increment serial| D2

    D1 -->|Waiting 2P→1R; omit held| P2
    D3 -->|Open serving call per window| P2
    D4 -->|Windows, groups| P2

    D4 -->|Window, service| P3
    D1 -->|Next waiting, hold list| P3
    D3 -->|Open call| P3
    P3 -->|Update ticket status| D1
    P3 -->|Insert or finish call| D3

    D4 -->|Users, windows, students, settings| P4
    D1 -->|Tickets, filters| P4
    D3 -->|Served history done only| P4
    P4 -->|Update users, students, walk-in PIN| D4
```

### Data store definitions

| ID | Maps to tables (logical) | Contents |
|----|---------------------------|----------|
| **D1** | `queues` | Ticket rows: number, service, optional `student_id`, optional `issued_by`/`issue_reason` (walk-in), priority, status (`waiting`/`serving`/`done`/`cancelled`/`held`), date |
| **D2** | `daily_queue_counters` | Per service per day: last issued serial |
| **D3** | `queue_calls` | Links queue to window: called / finished times (open = `finished_time` null) |
| **D4** | `services`, `windows`, `users`, `students`, `settings` | Service prefixes, window groups, accounts (guard issuer cannot log in), Student ID allowlist, kiosk walk-in PIN |

### Process definitions

| ID | Name | Typical controller / route |
|----|------|------------------------------|
| **1.0** | Issue ticket | `KioskController@store`, `GET /kiosk`, `GET /kiosk/student`, `POST /kiosk/walk-in/unlock`, `POST /kiosk/walk-in` |
| **2.0** | Produce display data | `DisplayController@index`, `DisplayController@data` |
| **3.0** | Serve at window | `WindowController` call-next / recall / complete / hold / call-held, `GET /window` |
| **4.0** | Administer system | `AdminDashboardController`, `UserManagementController`, `StudentManagementController`, `SettingsController`, `GuardIssueController`, `HistoryController` |

### External entities

| Entity | Role |
|--------|------|
| **Customer** | Uses public kiosk (Student ID or walk-in PIN); no login |
| **Staff** | Logged-in counter user tied to one window |
| **Administrator** | Full admin routes, including kiosk PIN and optional `/guard` issue |
| **Display client** | Browser on TV/monitor; read-only public display |

---

## Level 0 (context) — optional one-page summary

For documentation sets that require a **context diagram**: draw a single process **0.0 Queue management system** with the same four external entities and a single bidirectional “queue data” bundle to MySQL; this Level 1 diagram is the decomposition of **0.0**.

---

*PECIT queue system — aligns with `routes/web.php` and domain tables.*
