# Queue system — detailed system flowcharts

Mermaid diagrams for the **print-only** PECIT queue system (Laravel 12, Blade, MySQL). Student ID is collected on kiosk confirm; names are staff-only.

Interactive Archify architecture map (open in a browser): [docs/archify/pecit-runtime.architecture.html](archify/pecit-runtime.architecture.html).

**View Mermaid in browser:** with `php artisan serve` running, open  
[http://127.0.0.1:8000/flowchart-viewer.html](http://127.0.0.1:8000/flowchart-viewer.html)  
(or `/flowchart-viewer.html` on your app host).  

Also render in **GitHub**, **VS Code** (Mermaid preview), or [mermaid.live](https://mermaid.live) by pasting diagrams from this file.

---

## Table of contents

0. [Master system overview](#0-master-system-overview-similar-style-to-classic-kiosk-flow)
1. [End-to-end context](#1-end-to-end-context)
2. [Kiosk — ticket issuance](#2-kiosk--ticket-issuance)
3. [Public display monitor](#3-public-display-monitor-detailed)
4. [Staff window — counter](#4-staff-window--counter-detailed)
5. [Admin panel](#5-admin-panel-detailed)
6. [Queue lifecycle & shared data](#6-queue-lifecycle--shared-data)
7. [Auth & middleware](#7-auth--middleware)

---

## 0. Master system overview (similar style to classic kiosk flow)

Single end-to-end picture aligned with **this** codebase: **Student ID on confirm**, **print/display stay number-only**, **print-only** (no eco/photo path), **display** + **voice**, **staff** writes to the same tables.

**Printing:** This diagram is **compact** so it fits **Letter/A4 bond paper** when exported from [mermaid.live](https://mermaid.live) (SVG/PNG) or printed: use **Landscape** if needed, or **scale to fit** in the print dialog.

```mermaid
%%{init: {'flowchart': {'nodeSpacing': 28, 'rankSpacing': 36, 'padding': 6, 'useMaxWidth': true}, 'themeVariables': { 'fontSize': '11px'}}}%%
flowchart TD
    St([Start]) --> K["Kiosk: service → Regular/Priority → confirm ID + ETA"]
    K --> DB[("DB: new queue + daily counter")]
    DB --> PA["Customer: ticket #, print, countdown, then wait"]
    DB --> PB["TV: now serving + waiting 2P→1R + voice"]
    PA --> SW["Staff: next / recall / complete / hold + timer"]
    PB --> SW
    SW --> Fair["FairQueueScheduler shared per service"]
    Fair --> DB
    SW --> Q{Done?}
    Q -->|Recall / same ticket| SW
    Q -->|Complete| H["Save queue_calls + done"]
    H --> En([End])
```

---

## 1. End-to-end context

```mermaid
flowchart TB
    subgraph Public["Public — no login"]
        K["/kiosk — KioskController"]
        D["/display — DisplayController"]
    end
    subgraph AuthLogin["Login /logout"]
        LG["/login — LoginController"]
        LO["POST /logout"]
    end
    subgraph Staff["Staff — auth + staff middleware"]
        SW["/window — WindowController"]
    end
    subgraph Admin["Admin — auth + admin middleware"]
        AD["/admin — AdminDashboardController"]
        UM["/admin/users — UserManagementController"]
        HI["/admin/history* — HistoryController"]
    end
    DB[(MySQL)]
    K --> DB
    D --> DB
    SW --> DB
    AD --> DB
    UM --> DB
    HI --> DB
    LG --> DB
```

---

## 2. Kiosk — ticket issuance

```mermaid
flowchart TD
    Start([Visitor: /kiosk]) --> S1["Step 1: Select service<br/>services excluding Promissory in KioskController@index"]
    S1 --> S2["Step 2: Regular or Priority<br/>hidden input priority"]
    S2 --> S3["Step 3: Confirm — Student ID keypad + ETA"]
    S3 --> POST["POST /kiosk — KioskController@store"]
    POST --> V{"Validate service_id, priority, student_id"}
    V -->|Fail| E["Show validation errors on step 3"]
    V -->|OK| Txn["DB transaction"]
    subgraph Trn["Transaction"]
        T0["Lock student; reject unknown / already queued"]
        T1["Lock/update daily_queue_counters<br/>per service + today"]
        T2["Insert queues row<br/>student_id, priority 0|1, status waiting"]
    end
    Txn --> Print["View kiosk.printing<br/>issuedAt, priorityLabel"]
    Print --> AP["window.print + countdown"]
    AP --> Home([Back to /kiosk])
```

---

## 3. Public display monitor (detailed)

```mermaid
flowchart TD
    subgraph InitialLoad["First paint — GET /display"]
        A1["DisplayController@index"]
        A2["getDisplayData — same logic as JSON"]
        A3["view display.index<br/>nowServingGroups, waiting"]
    end
    A1 --> A2 --> A3

    subgraph DataBuild["getDisplayData — server"]
        B1["Load all windows orderBy id"]
        B2["groupBy trim group_name<br/>natural sort keys"]
        B3["Per group: sort windows by name<br/>natural order"]
        B4["Per window: QueueService<br/>latestCallForWindow today"]
        B5["queueNumberFromCall or<br/>placeholder ----"]
        B6["call_token = id + called_time<br/>for TTS refresh / recall"]
        B7["Global waiting: queues.status=waiting<br/>today, order priority desc, queue_number"]
        B8["Map priority bool to Priority/Regular label"]
    end
    A2 --> B1 --> B2 --> B3 --> B4 --> B5
    B4 --> B6
    B7 --> B8

    subgraph PollLoop["Browser — display/index.blade.php"]
        C1["setInterval ~3s"]
        C2["GET /display/data — JSON"]
        C3["renderNowServing groups<br/>horizontal columns + cashier row"]
        C4["renderWaiting table rows"]
        C5["maybeAnnounce: compare call_token<br/>per window_key"]
        C6["speechSynthesis queue<br/>unlock on click/touch/keydown"]
    end
    A3 --> C1
    C1 --> C2 --> C3 --> C4 --> C5 --> C6
    C2 -->|"same getDisplayData()"| A2
```

### Display — UI blocks (conceptual)

```mermaid
flowchart LR
    subgraph Header["Header bar"]
        H1["Logo + school name"]
        H2["dateTimeDisplay clock"]
    end
    subgraph NowServing["NOW SERVING"]
        G1["Group pill e.g. Window 1"]
        G2["Per window: label + big number"]
        G3["Window 1: 3 cashiers horizontal"]
    end
    subgraph Waiting["Waiting list"]
        W1["Table Queue # / Priority"]
        W2["scroll max-height"]
    end
    Header --> NowServing --> Waiting
```

---

## 4. Staff window — counter (detailed)

```mermaid
flowchart TD
    subgraph Gate["Access"]
        U1["User must be staff"]
        U2["user.window_id required"]
        U3["403 if no window"]
    end

    subgraph Page["GET /window — WindowController@index"]
        P1["Load Window + service"]
        P2["Latest QueueCall today for window<br/>→ current ticket"]
        P3["Next waiting: same service_id<br/>today, priority desc, id"]
        P4["waitingTickets: up to 10<br/>priority desc, queue_number"]
        P5["staff.window view<br/>buttons above table"]
    end
    Gate --> Page

    subgraph Poll["GET /window/state — JSON"]
        S1["getWindowState window_id"]
        S2["current from latest open call"]
        S3["next preview + waiting_list 10 rows"]
    end
    Page --> Poll

    subgraph Actions["POST actions"]
        CN["POST /window/call-next"]
        RC["POST /window/recall"]
        CP["POST /window/complete"]
        HD["POST /window/hold"]
        CH["POST /window/call-held"]
    end

    CN --> CNstep["Transaction"]
    subgraph CallNextTx["callNext transaction"]
        X1["If open call: set finished_time<br/>queue status done"]
        X2["Pick next waiting same service<br/>today priority desc, id"]
        X3["Set queue serving + QueueCall<br/>called_time now"]
    end

    RC --> RC1["Latest call today<br/>bump called_time now<br/>display + TTS see new token"]

    CP --> CP1["Set finished_time<br/>queue done"]

    HD --> HD1["Set finished_time<br/>queue held; window free"]
    CH --> CH1["Require no open serving ticket<br/>status serving + new queue_call"]

    subgraph Shortcut["Keyboard"]
        K1["Alt+N → Call Next<br/>Alt+R → Recall<br/>Alt+C → Complete"]
    end
    Shortcut --> CN

    subgraph StaffHistory["GET /window/history"]
        H1["queue_calls join queues, services<br/>filter optional date"]
        H2["Paginate staff window only"]
    end
```

### Staff — action sequence (call next)

```mermaid
sequenceDiagram
    participant Staff
    participant Browser
    participant Laravel
    participant DB

    Staff->>Browser: Click Call Next
    Browser->>Laravel: POST /window/call-next
    Laravel->>DB: Begin transaction
    Laravel->>DB: Close open call + mark queue done if any
    Laravel->>DB: Select next waiting queue FOR UPDATE
    Laravel->>DB: Update queue serving, insert queue_calls
    Laravel->>DB: Commit
    Laravel-->>Browser: Redirect back + flash
    Browser->>Laravel: Poll GET /window/state
    Laravel-->>Browser: current, next, waiting_list
```

---

## 5. Admin panel (detailed)

```mermaid
flowchart TD
    subgraph AdminDash["GET /admin — AdminDashboardController@index"]
        D1["Counts today: total, waiting, completed"]
        D2["admin.dashboard view"]
    end

    subgraph WaitingJson["GET /admin/queues/waiting"]
        W1["JSON all waiting queues today<br/>with service"]
        W2["Dashboard live table AJAX"]
    end

    subgraph Users["Resource /admin/users — UserManagementController"]
        U1["staff → window_id required"]
        U2["unique window: one staff per window"]
        U3["admin → window_id null"]
        U4["password hashed"]
    end

    subgraph History["HistoryController — admin only"]
        H1["GET /admin/history — served queue_calls<br/>staff name via users.window_id"]
        H2["GET /admin/history/tickets — all queues rows"]
        H3["GET /admin/history/reports — analytics date range"]
        H4["GET/PUT/DELETE history edit — single queue_call"]
    end

    subgraph AdminFlow["Typical flow"]
        A1["Login /login as admin"]
        A2["Dashboard overview"]
        A3["Manage users & windows"]
        A4["Audit history / tickets / reports"]
    end
    A1 --> A2 --> A3 --> A4
```

### Admin — history & reports (data sources)

```mermaid
flowchart LR
    subgraph Tables["Primary tables"]
        QC[queue_calls]
        Q[queues]
        SV[services]
        WIN[windows]
        US[users]
    end
    HI["admin/history"] --> QC
    HI --> Q
    HI --> SV
    HI --> WIN
    HI --> US
    TK["admin/history/tickets"] --> Q
    TK --> SV
    RP["admin/history/reports"] --> QC
    RP --> Q
    RP --> SV
```

---

## 6. Queue lifecycle & shared data

```mermaid
stateDiagram-v2
    [*] --> waiting: Kiosk creates ticket
    waiting --> serving: Staff Call Next
    serving --> done: Staff Complete<br/>or Call Next auto-completes previous
    serving --> held: Staff Hold
    held --> serving: Staff Call held
    done --> [*]
    note right of serving: Recall updates called_time<br/>same queue_number, new token
```

```mermaid
flowchart LR
    subgraph Writes["Writes"]
        K["kiosk.store"] --> Q[(queues)]
        K --> D[(daily_queue_counters)]
        CN["call-next"] --> Q
        CN --> QC[(queue_calls)]
        CP["complete"] --> Q
        CP --> QC
    end
    subgraph Reads["Reads"]
        DISP["display.data"] --> Q
        DISP --> WIN[(windows)]
        DISP --> QC
        ST["window.state"] --> Q
        ST --> QC
    end
```

---

## 7. Auth & middleware

```mermaid
flowchart TD
    L["POST /login"] --> Auth["Session auth"]
    Auth --> R1{"role?"}
    R1 -->|admin| ADM["prefix /admin/*"]
    R1 -->|staff| STF["prefix /window/*"]
    R1 -->|wrong| DENY["Redirect /login"]
    LO["POST /logout"] --> OUT["Session cleared"]
```

---

*Generated for the PECIT queue system. Routes: `routes/web.php`.*
