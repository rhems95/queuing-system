# Level 1 data flow diagram — queue system

**Context:** Physical **system boundary** = the Laravel web application + MySQL used by kiosk, display, staff, and admin.  
**Level 1** decomposes that system into **four major processes**, four **logical data stores**, and **four external entities**.

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

    EC -->|Service choice, priority| P1
    P1 -->|Queue number, service| EC

    ED -->|Poll /display, /display/data| P2
    P2 -->|Now serving, waiting list| ED

    ES -->|Call next, recall, complete| P3
    P3 -->|Flash / status| ES

    EA -->|Users, filters, reports| P4
    P4 -->|Screens, exports| EA

    D4 -->|Valid services| P1
    P1 -->|New ticket row| D1
    P1 -->|Increment serial| D2

    D1 -->|Waiting, by date| P2
    D3 -->|Latest call per window| P2
    D4 -->|Windows, groups| P2

    D4 -->|Window, service| P3
    D1 -->|Next waiting, status| P3
    D3 -->|Open call| P3
    P3 -->|Update ticket status| D1
    P3 -->|Insert or update call| D3

    D4 -->|Users, windows| P4
    D1 -->|Tickets, filters| P4
    D3 -->|Served history| P4
    P4 -->|Update users| D4
```

### Data store definitions

| ID | Maps to tables (logical) | Contents |
|----|---------------------------|----------|
| **D1** | `queues` | Ticket rows: number, service, priority, status, date |
| **D2** | `daily_queue_counters` | Per service per day: last issued serial |
| **D3** | `queue_calls` | Links queue to window: called / finished times |
| **D4** | `services`, `windows`, `users` | Service prefixes, window groups, accounts |

### Process definitions

| ID | Name | Typical controller / route |
|----|------|------------------------------|
| **1.0** | Issue ticket | `KioskController@store`, `GET /kiosk` |
| **2.0** | Produce display data | `DisplayController@index`, `DisplayController@data` |
| **3.0** | Serve at window | `WindowController` call-next / recall / complete, `GET /window` |
| **4.0** | Administer system | `AdminDashboardController`, `UserManagementController`, `HistoryController` |

### External entities

| Entity | Role |
|--------|------|
| **Customer** | Uses public kiosk; no login |
| **Staff** | Logged-in counter user tied to one window |
| **Administrator** | Full admin routes |
| **Display client** | Browser on TV/monitor; read-only public display |

---

## Level 0 (context) — optional one-page summary

For documentation sets that require a **context diagram**: draw a single process **0.0 Queue management system** with the same four external entities and a single bidirectional “queue data” bundle to MySQL; this Level 1 diagram is the decomposition of **0.0**.

---

*PECIT queue system — aligns with `routes/web.php` and domain tables.*
