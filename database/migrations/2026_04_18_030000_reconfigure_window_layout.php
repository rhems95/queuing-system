<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cashierId = $this->upsertService('Cashier', 'C', 'Handles payments and cashier transactions');
        $promissoryId = $this->upsertService('Promissory Notes', 'P', 'Handles promissory note processing');
        $dmoId = $this->upsertService('Data Management Office', 'D', 'Handles data management transactions');
        $registrarId = $this->upsertService('Registrar', 'R', 'Handles registrar transactions');

        // Window 1 split into three cashier counters.
        $this->upsertWindow(1, 'Cashier 1', 'Window 1', $cashierId);
        $this->upsertWindow(2, 'Cashier 2', 'Window 1', $cashierId);
        $this->upsertWindow(3, 'Cashier 3', 'Window 1', $cashierId);

        // Dedicated windows.
        $this->upsertWindow(4, 'Promissory Notes', 'Window 2', $promissoryId);
        $this->upsertWindow(5, 'DMO', 'Window 3', $dmoId);
        $this->upsertWindow(6, 'Registrar', 'Window 4', $registrarId);
    }

    public function down(): void
    {
        // Keep existing data on rollback to avoid destructive resets.
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

    private function upsertWindow(int $id, string $windowName, string $groupName, int $serviceId): void
    {
        $exists = DB::table('windows')->where('id', $id)->exists();

        $payload = [
            'window_name' => $windowName,
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
};
