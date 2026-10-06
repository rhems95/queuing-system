# Queue system — detailed system flowcharts

Mermaid diagrams for the **print-only** PECIT queue system (Laravel 12, Blade, MySQL). Student ID is collected on kiosk confirm by keypad or ACR122U tap; names are staff-only. Walk-ins use the kiosk corner PIN (`settings.walkin_pin`) and may add a Philippine mobile. Optional SIM7600 SMS is number-only. Guard accounts cannot log in.

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

Single end-to-end picture aligned with **this** codebase: **Student ID on confirm**, **walk-in PIN on the kiosk** (no Guard login), **print/display stay number-only**, **print-only** (no eco/photo path), **display** + **voice**, **staff** writes to the same tables.

**Printing:** This diagram is **compact** so it fits **Letter/A4 bond paper** when exported from [mermaid.live](https://mermaid.live) (SVG/PNG) or printed: use **Landscape** if needed, or **scale to fit** in the print dialog.

```mermaid
%%{init: {'flowchart': {'nodeSpacing': 28, 'rankSpacing': 36, 'padding': 6, 'useMaxWidth': true}, 'themeVariables': { 'fontSize': '11px'}}}%%
flowchart TD
    St([Start]) --> K["Kiosk: service → priority → ID or card tap + ETA"]
    St --> W["Walk-in: corner PIN, optional PH mobile"]
    K --> DB[("DB: new queue + daily counter")]
    W --> DB
    DB --> PA["58mm ticket, countdown, then wait"]
    DB --> SMS["Opt-in text: ticket number"]
    DB --> PB["TV: now serving + waiting 2P→1R + voice"]
    PA --> SW["Staff: next / recall / complete / hold"]
    PB --> SW
    SW --> Fair["FairQueueScheduler shared per service"]
    Fair --> DB
    SW --> Note["Text window name; text tickets now within 10"]
    Note --> DB
    SW --> Q{Done?}
    Q -->|Recall| SW
    Q -->|Complete| H["Save queue_calls + done"]
    H --> En([End])
```

---

## 1. End-to-end context

```mermaid
flowchart TB
    subgraph Public["Public — no login"]
        K["/kiosk — KioskController"]
        KW["/kiosk/walk-in — PIN unlock + issue"]
        D["/display — DisplayController"]
    end
    subgraph AuthLogin["Login /logout"]
        LG["/login — LoginController<br/>admin/staff only; guard blocked"]
        LO["POST /logout"]
    end
    subgraph Staff["Staff — auth + staff middleware"]
        SW["/window — WindowController"]
    end
    subgraph Admin["Admin — auth + admin middleware"]
        AD["/admin — AdminDashboardController"]
        UM["/admin/users — UserManagementController"]
        STU["/admin/students — StudentManagementController"]
        SET["/admin/settings — SettingsController"]
        GI["/guard — GuardIssueController"]
        HI["/admin/history* — HistoryController"]
    end
    DB[(MySQL)]
    K --> DB
    KW --> DB
    D --> DB
    SW --> DB
    AD --> DB
    UM --> DB
    STU --> DB
    SET --> DB
    GI --> DB
    HI --> DB
    LG --> DB
```

---

## 2. Kiosk — ticket issuance

```mermaid
flowchart TD
    Start([Visitor: /kiosk]) --> Path{Ticket type}
    Path -->|Student| S1["Step 1: Select service<br/>Promissory hidden"]
    S1 --> S2["Step 2: Regular or Priority"]
    S2 --> S3["Step 3: keypad or ACR122U tap + ETA"]
    S3 --> Look{"GET /kiosk/student"}
    Look -->|Unknown or already queued| E["Confirm stays disabled"]
    Look -->|Found| Opt{"Notify me by SMS checked?"}
    Opt --> POST["POST /kiosk"]
    POST --> Txn["Lock student; insert waiting ticket<br/>copy stored mobile only if checked"]
    Path -->|Walk-in| PinBtn["Corner button → PIN pad"]
    PinBtn --> Unlock{"PIN matches settings.walkin_pin"}
    Unlock -->|No| PinFail["Reject"]
    Unlock -->|Yes| Overlay["Service, priority, reason<br/>optional mobile pad"]
    Overlay --> Mob{"Blank or 09 + 9 digits?"}
    Mob -->|No| Block["Issue and Print disabled"]
    Mob -->|Yes| WPOST["POST /kiosk/walk-in"]
    WPOST --> WTxn["Insert walk-in ticket<br/>token T1006ABC, three letters"]
    Txn --> Print
    WTxn --> Print["58mm number-only ticket"]
    Print --> Text{"sms_mobile set?"}
    Text -->|Yes| Out["Enqueue ticket text<br/>walk-in text includes the token"]
    Text -->|No| AP
    Out --> AP["window.print + countdown<br/>walk-in token on screen only"]
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
        B7["Waiting: FairQueueScheduler orderedWaiting<br/>today, omit held, max 10"]
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
        P2["Latest open QueueCall today for window<br/>→ current ticket"]
        P3["Next waiting: FairQueueScheduler peek<br/>same service_id, 2P→1R"]
        P4["waitingTickets: up to 10 fair order<br/>heldTickets: Call held list"]
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
        X2["FairQueueScheduler claimNextWaiting<br/>2P→1R, this window_id on queue_calls"]
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
    Laravel->>Laravel: Enqueue called text and 10-away texts
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
        U5["guard role cannot log in; issuer row only"]
    end

    subgraph Students["/admin/students — StudentManagementController"]
        S1["Add / delete allowlisted Student IDs"]
        S2["CSV import student_id,name,card_uid,mobile"]
        S3["Cannot delete if open ticket today"]
    end

    subgraph Settings["GET/PUT /admin/settings — SettingsController"]
        P1["Edit walkin_pin in settings table"]
        P2["4–6 digits; not stored in .env"]
        P3["paper_sms_contacts: name, PH mobile, Send SMS checkbox"]
        P4["One paper-out text per outage to checked numbers"]
    end

    subgraph WalkInAdmin["GET/POST /guard — GuardIssueController"]
        G1["Admin-only walk-in issue"]
        G2["Same TicketIssuer as kiosk PIN path"]
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
        A3["Manage users, students, kiosk PIN, paper SMS list"]
        A4["Audit history / tickets / reports; optional /guard issue"]
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
        STU[students]
        SET[settings]
    end
    HI["admin/history"] --> QC
    HI --> Q
    HI --> SV
    HI --> WIN
    HI --> US
    TK["admin/history/tickets"] --> Q
    TK --> SV
    STUL["admin/students"] --> STU
    PIN["admin/settings"] --> SET
    RP["admin/history/reports"] --> QC
    RP --> Q
    RP --> SV
```

---

## 6. Queue lifecycle & shared data

```mermaid
stateDiagram-v2
    [*] --> waiting: Kiosk or walk-in creates ticket
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
        K["kiosk.store / kiosk.walkInStore"] --> Q[(queues)]
        K --> D[(daily_queue_counters)]
        CN["call-next"] --> Q
        CN --> QC[(queue_calls)]
        HD["hold / call-held"] --> Q
        HD --> QC
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
    R1 -->|guard| BLOCK["No login — use kiosk PIN"]
    R1 -->|wrong| DENY["Redirect /login"]
    LO["POST /logout"] --> OUT["Session cleared"]
```

---

*Generated for the PECIT queue system. Routes: `routes/web.php`.*
