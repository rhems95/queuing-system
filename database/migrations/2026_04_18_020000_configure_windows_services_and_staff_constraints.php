<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $cashierId = $this->upsertService('Cashier', 'C', 'Handles payments and financial transactions');
        $dmoId = $this->upsertService('Data Management Office', 'D', 'Handles data management transactions');
        $registrarId = $this->upsertService('Registrar', 'R', 'Handles student records and documents');

        $this->upsertWindow(1, 'Cashier 1', 'Cashier', $cashierId);
        $this->upsertWindow(2, 'Cashier 2', 'Cashier', $cashierId);
        $this->upsertWindow(3, 'DMO', 'DMO', $dmoId);
        $this->upsertWindow(4, 'Registrar', 'Registrar', $registrarId);

        if (! $this->hasUniqueIndex('users', 'users_window_id_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('window_id', 'users_window_id_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->hasUniqueIndex('users', 'users_window_id_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_window_id_unique');
            });
        }
    }

    private function upsertService(string $name, string $prefix, string $description): int
    {
        $id = DB::table('services')->where('service_name', $name)->value('id');

        if ($id) {
            DB::table('services')->where('id', $id)->update([
                'prefix' => $prefix,
                'description' => $description,
            ]);

            return (int) $id;
        }

        return (int) DB::table('services')->insertGetId([
            'service_name' => $name,
            'prefix' => $prefix,
            'description' => $description,
        ]);
    }

    private function upsertWindow(int $id, string $name, string $groupName, int $serviceId): void
    {
        $exists = DB::table('windows')->where('id', $id)->exists();

        $payload = [
            'window_name' => $name,
            'group_name' => $groupName,
            'service_id' => $serviceId,
            'status' => 'active',
        ];

        if ($exists) {
            DB::table('windows')->where('id', $id)->update($payload);
        } else {
            DB::table('windows')->insert(array_merge(['id' => $id], $payload));
        }
    }

    private function hasUniqueIndex(string $table, string $indexName): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(1) AS aggregate FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $indexName]
        );

        return ((int) ($result->aggregate ?? 0)) > 0;
    }
};
