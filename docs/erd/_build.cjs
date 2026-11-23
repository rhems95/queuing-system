const { spawnSync } = require("child_process");
const fs = require("fs");
const path = require("path");

const root = path.join(__dirname, "../..");
const cli = path.join(
  root,
  ".agents/skills/erd-designer/scripts/erd-cli.cjs"
);
const erd = path.join(__dirname, "queuing_system.erd");

function parseJson(text, tool) {
  const trimmed = (text || "").trim();
  if (!trimmed) return {};
  try {
    return JSON.parse(trimmed);
  } catch (_) {
    const start = trimmed.search(/[{[]/);
    if (start >= 0) {
      return JSON.parse(trimmed.slice(start));
    }
    throw new Error(`${tool}: not JSON\n${trimmed.slice(0, 1200)}`);
  }
}

function run(tool, args) {
  const r = spawnSync(
    process.execPath,
    [cli, "run", tool, "--file", erd, "--args", JSON.stringify(args)],
    { encoding: "utf8", maxBuffer: 20 * 1024 * 1024 }
  );
  if (r.status !== 0) {
    throw new Error(`${tool} failed:\n${r.stdout}\n${r.stderr}`);
  }
  return parseJson(r.stdout, tool);
}

function tableIdFromLink(link) {
  const item = Array.isArray(link) ? link[0] : link;
  const uri = item?.uri || "";
  const m = uri.match(/\/tables\/([^/?]+)$/);
  if (!m) throw new Error(`No tableId in ${JSON.stringify(link)}`);
  return m[1];
}

function col(physical, logical, typeId, extra = {}) {
  const share = {
    columnName: { physical, logical },
    columnTypeId: typeId,
  };
  if (extra.precision != null) share.precision = String(extra.precision);
  if (extra.unsigned != null) share.unsigned = extra.unsigned;
  if (extra.description) share.description = extra.description;
  if (extra.optionExpression) share.optionExpression = extra.optionExpression;
  const column = { columnShare: share };
  if (extra.pk) column.primaryKey = true;
  if (extra.notNull != null) column.notNull = extra.notNull;
  if (extra.unique) column.unique = true;
  if (extra.ai) column.autoIncrement = true;
  if (extra.defaultValue != null) column.defaultValue = extra.defaultValue;
  return column;
}

const T = {
  bigint: 17,
  int: 15,
  tinyint: 10,
  varchar: 312,
  text: 322,
  date: 101,
  datetime: 112,
  timestamp: 115,
  enum: 2101,
};

const colors = {
  services: { background: "#2563EB", foreground: "#FFFFFF" },
  windows: { background: "#0D9488", foreground: "#FFFFFF" },
  users: { background: "#BE185D", foreground: "#FFFFFF" },
  queues: { background: "#16A34A", foreground: "#FFFFFF" },
  students: { background: "#0F766E", foreground: "#FFFFFF" },
  queue_calls: { background: "#D97706", foreground: "#FFFFFF" },
  daily_queue_counters: { background: "#7C3AED", foreground: "#FFFFFF" },
  settings: { background: "#475569", foreground: "#FFFFFF" },
};

function addTable(physical, logical, description, x, y, columns) {
  const link = run("add-table", {
    table: {
      tableName: { physical, logical },
      description,
      characterSet: "utf8mb4",
      collate: "utf8mb4_general_ci",
      columns,
      view: {
        position: { x, y },
        color: colors[physical],
      },
    },
  });
  return tableIdFromLink(link);
}

{
  const existing = run("list-tables", {});
  if ((existing.items || []).length) {
    console.error(
      "queuing_system.erd already has tables. Delete the file, recreate it with create-document, then re-run this script."
    );
    process.exit(1);
  }
}

const tables = {};
tables.services = addTable(
  "services",
  "Service",
  "Ticket types (Cashier, Registrar, DMO, Promissory Notes).",
  -480,
  -280,
  [
    col("id", "ID", T.bigint, { unsigned: true, pk: true, notNull: true, ai: true }),
    col("service_name", "Name", T.varchar, { precision: 50, notNull: true }),
    col("prefix", "Prefix", T.varchar, { precision: 5, notNull: true, description: "Ticket code letter, e.g. C" }),
    col("description", "Description", T.text, { notNull: false }),
    col("created_at", "Created at", T.timestamp, { notNull: false }),
    col("updated_at", "Updated at", T.timestamp, { notNull: false }),
  ]
);

tables.windows = addTable(
  "windows",
  "Window",
  "Staff counters. Cashier 1–3 share service_id.",
  -80,
  -280,
  [
    col("id", "ID", T.bigint, { unsigned: true, pk: true, notNull: true, ai: true }),
    col("window_name", "Name", T.varchar, { precision: 50, notNull: true }),
    col("group_name", "Display group", T.varchar, { precision: 50, notNull: false }),
    col("service_id", "Service ID", T.bigint, { unsigned: true, notNull: true }),
    col("status", "Status", T.enum, {
      notNull: false,
      defaultValue: "'active'",
      optionExpression: "('active','inactive')",
    }),
    col("created_at", "Created at", T.timestamp, { notNull: false }),
    col("updated_at", "Updated at", T.timestamp, { notNull: false }),
  ]
);

tables.users = addTable(
  "users",
  "User",
  "Admin or staff login; guard is issuer-only (cannot log in). Staff window_id is unique (one account per counter).",
  320,
  -280,
  [
    col("id", "ID", T.bigint, { unsigned: true, pk: true, notNull: true, ai: true }),
    col("name", "Name", T.varchar, { precision: 100, notNull: true }),
    col("email", "Email", T.varchar, { precision: 100, notNull: true, unique: true }),
    col("password", "Password", T.varchar, { precision: 255, notNull: true }),
    col("role", "Role", T.enum, {
      notNull: true,
      optionExpression: "('admin','staff','guard')",
    }),
    col("window_id", "Window ID", T.bigint, { unsigned: true, notNull: false, unique: true }),
    col("created_at", "Created at", T.timestamp, { notNull: false }),
    col("updated_at", "Updated at", T.timestamp, { notNull: false }),
  ]
);

tables.students = addTable(
  "students",
  "Student",
  "Allowlisted kiosk IDs. Name is staff/admin only; never printed.",
  -480,
  280,
  [
    col("id", "ID", T.bigint, { unsigned: true, pk: true, notNull: true, ai: true }),
    col("student_id", "Student ID", T.varchar, { precision: 32, notNull: true, unique: true }),
    col("name", "Name", T.varchar, { precision: 150, notNull: true }),
    col("created_at", "Created at", T.timestamp, { notNull: false }),
    col("updated_at", "Updated at", T.timestamp, { notNull: false }),
  ]
);

tables.queues = addTable(
  "queues",
  "Queue ticket",
  "Number-only on print/display. Optional student_id; walk-ins use issued_by / issue_reason. Name is not stored on the ticket.",
  -80,
  80,
  [
    col("id", "ID", T.bigint, { unsigned: true, pk: true, notNull: true, ai: true }),
    col("queue_number", "Queue number", T.varchar, { precision: 20, notNull: true }),
    col("service_id", "Service ID", T.bigint, { unsigned: true, notNull: true }),
    col("student_id", "Student ID", T.varchar, { precision: 32, notNull: false, description: "Allowlisted ID; name lives on students" }),
    col("issued_by", "Issued by", T.bigint, { unsigned: true, notNull: false, description: "Kiosk/admin walk-in issuer user id" }),
    col("issue_reason", "Issue reason", T.varchar, { precision: 32, notNull: false, description: "Walk-in reason; null for student tickets" }),
    col("priority", "Priority", T.tinyint, {
      notNull: false,
      defaultValue: "0",
      description: "0 = regular, 1 = priority",
    }),
    col("status", "Status", T.enum, {
      notNull: false,
      defaultValue: "'waiting'",
      optionExpression: "('waiting','serving','done','cancelled','held')",
    }),
    col("queue_date", "Queue date", T.date, { notNull: true }),
    col("created_at", "Created at", T.timestamp, { notNull: false, description: "Used for average wait time" }),
    col("updated_at", "Updated at", T.timestamp, { notNull: false }),
  ]
);

tables.queue_calls = addTable(
  "queue_calls",
  "Queue call",
  "Open row while serving (finished_time IS NULL). Recall reuses this record.",
  320,
  80,
  [
    col("id", "ID", T.bigint, { unsigned: true, pk: true, notNull: true, ai: true }),
    col("queue_id", "Queue ID", T.bigint, { unsigned: true, notNull: true }),
    col("window_id", "Window ID", T.bigint, { unsigned: true, notNull: true }),
    col("called_time", "Called time", T.datetime, { notNull: false }),
    col("finished_time", "Finished time", T.datetime, { notNull: false }),
    col("created_at", "Created at", T.timestamp, { notNull: false }),
    col("updated_at", "Updated at", T.timestamp, { notNull: false }),
  ]
);

tables.daily_queue_counters = addTable(
  "daily_queue_counters",
  "Daily queue counter",
  "Locked increment per service and date for C001, R001, etc.",
  -480,
  80,
  [
    col("id", "ID", T.bigint, { unsigned: true, pk: true, notNull: true, ai: true }),
    col("service_id", "Service ID", T.bigint, { unsigned: true, notNull: true }),
    col("queue_date", "Queue date", T.date, { notNull: true }),
    col("last_number", "Last number", T.int, { notNull: false, defaultValue: "0" }),
    col("created_at", "Created at", T.timestamp, { notNull: false }),
    col("updated_at", "Updated at", T.timestamp, { notNull: false }),
  ]
);

tables.settings = addTable(
  "settings",
  "Setting",
  "Key/value app settings. Seed walkin_pin = 1981; edited under Admin → Kiosk PIN.",
  320,
  280,
  [
    col("id", "ID", T.bigint, { unsigned: true, pk: true, notNull: true, ai: true }),
    col("setting_key", "Key", T.varchar, { precision: 64, notNull: true, unique: true }),
    col("setting_value", "Value", T.varchar, { precision: 255, notNull: true }),
    col("updated_at", "Updated at", T.timestamp, { notNull: false }),
  ]
);

function columnsByName(tableId) {
  const detail = run("find-table", { tableId });
  const map = {};
  for (const c of detail.columns || []) {
    const name = c.columnName?.physical || c.overrideName?.physical;
    const id = c.columnModelId || c.columnId;
    if (name && id) map[name] = id;
  }
  if (!Object.keys(map).length) {
    throw new Error(
      `Could not map columns for ${tableId}: ${JSON.stringify(detail).slice(0, 1200)}`
    );
  }
  return map;
}

const cols = {};
for (const [name, id] of Object.entries(tables)) {
  cols[name] = columnsByName(id);
}

function relate(name, parent, child, parentCol, childCol, parentCard, childCard) {
  run("create-relation", {
    relation: {
      relationName: name,
      parentTableId: tables[parent],
      childTableId: tables[child],
      parentCardinality: parentCard,
      childCardinality: childCard,
      relationPairs: [
        {
          parentColumnId: cols[parent][parentCol],
          childColumnId: cols[child][childCol],
        },
      ],
    },
  });
}

relate("services_windows", "services", "windows", "id", "service_id", "1", "0..N");
relate("services_queues", "services", "queues", "id", "service_id", "1", "0..N");
relate("services_daily_queue_counters", "services", "daily_queue_counters", "id", "service_id", "1", "0..N");
relate("windows_users", "windows", "users", "id", "window_id", "1", "0..1");
relate("windows_queue_calls", "windows", "queue_calls", "id", "window_id", "1", "0..N");
relate("queues_queue_calls", "queues", "queue_calls", "id", "queue_id", "1", "0..N");
relate("students_queues", "students", "queues", "student_id", "student_id", "1", "0..N");
relate("users_queues", "users", "queues", "id", "issued_by", "1", "0..N");

run("add-table-index", {
  tableId: tables.queues,
  tableIndexes: [
    {
      tableIndex: {
        indexName: "idx_queue_date",
        indexColumns: [{ columnId: cols.queues.queue_date }],
      },
    },
  ],
});

run("add-memo", {
  memo: {
    memo: "Kiosk confirm collects Student ID; print and display stay number-only.\nWalk-in: kiosk PIN in settings.walkin_pin (no Guard login). Hold is not done.\nWait = created_at → called_time. Service = called_time → finished_time (done only).",
    position: { x: -480, y: 420 },
    size: { width: 420, height: 140 },
    color: { background: "#FFFBEB", foreground: "#1F2937" },
  },
});

const listed = run("list-tables", {});
console.log(JSON.stringify({ tables, listed }, null, 2));
