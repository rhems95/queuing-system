# Entity-relationship diagram (ERD)

Domain tables for the PECIT Queuing System (`queuing_system`).  
Also embedded in [README.md](../README.md).

```mermaid
erDiagram
    SERVICES ||--o{ WINDOWS : "has counters"
    SERVICES ||--o{ QUEUES : "ticket type"
    SERVICES ||--o{ DAILY_QUEUE_COUNTERS : "daily serial"
    WINDOWS ||--o| USERS : "one staff"
    WINDOWS ||--o{ QUEUE_CALLS : "calls at"
    QUEUES ||--o{ QUEUE_CALLS : "served as"

    SERVICES {
        bigint id PK
        varchar service_name
        varchar prefix
        text description
    }

    WINDOWS {
        bigint id PK
        varchar window_name
        varchar group_name
        bigint service_id FK
        enum status
    }

    USERS {
        bigint id PK
        varchar name
        varchar email
        varchar password
        enum role
        bigint window_id FK
    }

    DAILY_QUEUE_COUNTERS {
        bigint id PK
        bigint service_id FK
        date queue_date
        int last_number
    }

    QUEUES {
        bigint id PK
        varchar queue_number
        bigint service_id FK
        tinyint priority
        enum status
        date queue_date
        timestamp created_at
    }

    QUEUE_CALLS {
        bigint id PK
        bigint queue_id FK
        bigint window_id FK
        datetime called_time
        datetime finished_time
    }
```

## Notes

| Relationship | Meaning |
|--------------|---------|
| Service → Windows | One service can have many counters (e.g. Cashier 1–3) |
| Service → Queues | Shared waiting queue per service |
| Window → User | At most one staff account per window |
| Queue → Queue calls | Call/recall/complete sessions at a window |
| Service → Daily counters | Locked daily ticket serial (`C001`, …) |
