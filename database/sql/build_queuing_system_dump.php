<?php

/**
 * Rebuilds repo-root queuing_system.sql from the legacy dump:
 * - queues: drop student_name / student_id from structure and INSERT rows
 * - windows: add group_name; seed 6 windows (matches 2026_04_18_030000 migration intent)
 * - services: seed 4 services (matches migration intent)
 * - users: add UNIQUE users_window_id_unique on window_id
 *
 * Run from project root: php database/sql/build_queuing_system_dump.php
 */

$root = dirname(__DIR__, 2);
$src = $root . DIRECTORY_SEPARATOR . 'queuing_system.sql';
$dst = $root . DIRECTORY_SEPARATOR . 'queuing_system.sql';

if (! is_file($src)) {
    fwrite(STDERR, "Missing: {$src}\n");
    exit(1);
}

$sql = file_get_contents($src);

if (! str_contains($sql, "INSERT INTO `queues` (`id`, `student_name`")) {
    fwrite(STDERR, "Refusing to run: source dump must be the legacy format with student columns on queues.\nRestore the old queuing_system.sql from git, then run this script again.\n");
    exit(1);
}

// --- queues: replace CREATE TABLE ---
$queuesCreate = <<<'SQL'
CREATE TABLE `queues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue_number` varchar(20) NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `priority` tinyint(1) DEFAULT 0,
  `status` enum('waiting','serving','done','cancelled') DEFAULT 'waiting',
  `queue_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL;

$sql = preg_replace(
    '/CREATE TABLE `queues`\s*\([\s\S]*?\)\s*ENGINE=InnoDB[^;]*;/',
    rtrim($queuesCreate) . ';',
    $sql,
    1,
    $count
);
if ($count !== 1) {
    fwrite(STDERR, "Could not replace queues CREATE TABLE.\n");
    exit(1);
}

// --- queues: transform INSERT data ---
$insStart = strpos($sql, 'INSERT INTO `queues`');
if ($insStart === false) {
    fwrite(STDERR, "Could not find queues INSERT.\n");
    exit(1);
}
$insEnd = strpos($sql, ';', $insStart);
if ($insEnd === false) {
    fwrite(STDERR, "Could not find end of queues INSERT.\n");
    exit(1);
}
$fullInsert = substr($sql, $insStart, $insEnd - $insStart + 1);
$valuesPos = stripos($fullInsert, 'VALUES');
if ($valuesPos === false) {
    fwrite(STDERR, "Could not find VALUES in queues INSERT.\n");
    exit(1);
}
$insertBody = trim(substr($fullInsert, $valuesPos + strlen('VALUES')));
$insertBody = rtrim($insertBody, ';');

$newPrefix = "INSERT INTO `queues` (`id`, `queue_number`, `service_id`, `priority`, `status`, `queue_date`, `created_at`, `updated_at`) VALUES\n";

$lines = preg_split('/\R/', trim($insertBody));
$outRows = [];
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $line = rtrim($line, ',');
    if (! preg_match(
        '/^\((\d+),\s*(?:\'(?:\\\\.|[^\'])*\'|NULL),\s*(?:\'(?:\\\\.|[^\'])*\'|NULL),\s*\'((?:\\\\.|[^\'])*)\',\s*(\d+),\s*(\d+),\s*\'((?:\\\\.|[^\'])*)\',\s*\'((?:\\\\.|[^\'])*)\',\s*(NULL|\'(?:\\\\.|[^\'])*\'),\s*(NULL|\'(?:\\\\.|[^\'])*\')\)\s*,?$/',
        $line,
        $r
    )) {
        fwrite(STDERR, "Unparseable queues row:\n{$line}\n");
        exit(1);
    }
    $qn = str_replace("''", "'", $r[2]);
    $qn = str_replace("'", "''", $qn);
    $st = str_replace("''", "'", $r[5]);
    $st = str_replace("'", "''", $st);
    $outRows[] = sprintf(
        '(%s, \'%s\', %s, %s, \'%s\', \'%s\', %s, %s)',
        $r[1],
        $qn,
        $r[3],
        $r[4],
        $st,
        $r[6],
        $r[7],
        $r[8]
    );
}

$newInsert = $newPrefix . implode(",\n", $outRows) . ';';
$sql = substr_replace($sql, $newInsert, $insStart, $insEnd - $insStart + 1);

// --- windows: replace CREATE + INSERT ---
$windowsCreate = <<<'SQL'
CREATE TABLE `windows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `window_name` varchar(50) NOT NULL,
  `group_name` varchar(50) DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL;

$sql = preg_replace(
    '/CREATE TABLE `windows`\s*\([\s\S]*?\)\s*ENGINE=InnoDB[^;]*;/',
    rtrim($windowsCreate) . ';',
    $sql,
    1,
    $c2
);
if ($c2 !== 1) {
    fwrite(STDERR, "Could not replace windows CREATE TABLE.\n");
    exit(1);
}

$windowsInsert = <<<'SQL'
INSERT INTO `windows` (`id`, `window_name`, `group_name`, `service_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Cashier 1', 'Window 1', 1, 'active', NULL, NULL),
(2, 'Cashier 2', 'Window 1', 1, 'active', NULL, NULL),
(3, 'Cashier 3', 'Window 1', 1, 'active', NULL, NULL),
(4, 'Promissory Notes', 'Window 2', 2, 'active', NULL, NULL),
(5, 'DMO', 'Window 3', 3, 'active', NULL, NULL),
(6, 'Registrar', 'Window 4', 4, 'active', NULL, NULL);
SQL;

if (! preg_match('/INSERT INTO `windows`[^;]+;/s', $sql, $wm)) {
    fwrite(STDERR, "Could not find windows INSERT.\n");
    exit(1);
}
$sql = str_replace($wm[0], $windowsInsert, $sql);

// --- services: replace INSERT (keep CREATE) ---
$servicesInsert = <<<'SQL'
INSERT INTO `services` (`id`, `service_name`, `prefix`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Cashier', 'C', 'Handles payments and cashier transactions', NULL, NULL),
(2, 'Promissory Notes', 'P', 'Handles promissory note processing', NULL, NULL),
(3, 'Data Management Office', 'D', 'Handles data management transactions', NULL, NULL),
(4, 'Registrar', 'R', 'Handles registrar transactions', NULL, NULL);
SQL;

if (! preg_match('/INSERT INTO `services`[^;]+;/s', $sql, $sm)) {
    fwrite(STDERR, "Could not find services INSERT.\n");
    exit(1);
}
$sql = str_replace($sm[0], $servicesInsert, $sql);

// --- users: bump AUTO_INCREMENT for windows if needed ---
$sql = preg_replace(
    '/ALTER TABLE `windows`\s+MODIFY `id`[^;]+;/',
    "ALTER TABLE `windows`\n  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;",
    $sql,
    1
);

$sql = preg_replace(
    '/ALTER TABLE `services`\s+MODIFY `id`[^;]+;/',
    "ALTER TABLE `services`\n  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;",
    $sql,
    1
);

// --- users indexes: add unique on window_id after existing ALTER ---
if (strpos($sql, 'users_window_id_unique') === false) {
    $sql = str_replace(
        "ALTER TABLE `users`\n  ADD PRIMARY KEY (`id`),\n  ADD UNIQUE KEY `email` (`email`),\n  ADD KEY `window_id` (`window_id`);",
        "ALTER TABLE `users`\n  ADD PRIMARY KEY (`id`),\n  ADD UNIQUE KEY `email` (`email`),\n  ADD UNIQUE KEY `users_window_id_unique` (`window_id`),\n  ADD KEY `window_id` (`window_id`);",
        $sql
    );
    // MySQL allows duplicate index names on same column only if we remove duplicate KEY window_id - actually UNIQUE on window_id makes separate KEY redundant. Remove plain KEY window_id to avoid duplicate index on same column.
    $sql = str_replace(
        "ALTER TABLE `users`\n  ADD PRIMARY KEY (`id`),\n  ADD UNIQUE KEY `email` (`email`),\n  ADD UNIQUE KEY `users_window_id_unique` (`window_id`),\n  ADD KEY `window_id` (`window_id`);",
        "ALTER TABLE `users`\n  ADD PRIMARY KEY (`id`),\n  ADD UNIQUE KEY `email` (`email`),\n  ADD UNIQUE KEY `users_window_id_unique` (`window_id`);",
        $sql
    );
}

// --- header ---
$sql = preg_replace(
    '/-- Generation Time:.*$/m',
    '-- Generation Time: Apr 19, 2026 (regenerated to match Laravel domain migrations + anonymous queues)',
    $sql,
    1
);

file_put_contents($dst, $sql);
echo "Wrote {$dst}\n";
