-- Optional: run manually if `php artisan migrate` cannot run (existing DB / migration drift).
-- Safe to run once; will error if column already exists.

ALTER TABLE `windows` ADD COLUMN `group_name` VARCHAR(50) NULL AFTER `window_name`;
