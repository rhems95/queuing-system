<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';
                $table->id();
                $table->string('student_id', 50)->unique();
                $table->string('name', 100);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } elseif (Schema::hasTable('students')) {
            DB::statement('ALTER TABLE students CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        }

        if (Schema::hasTable('queues') && ! Schema::hasColumn('queues', 'student_id')) {
            Schema::table('queues', function (Blueprint $table) {
                $table->string('student_id', 50)->nullable()->after('service_id');
                $table->index('student_id', 'queues_student_id_index');
            });
        }

        DB::statement("ALTER TABLE queues MODIFY status ENUM('waiting','serving','done','cancelled','held') DEFAULT 'waiting'");

        $now = now();
        $seeds = [
            ['student_id' => '2024-0001', 'name' => 'Juan Dela Cruz'],
            ['student_id' => '2024-0002', 'name' => 'Maria Santos'],
            ['student_id' => '2024-0003', 'name' => 'Jose Rizal'],
            ['student_id' => '2024-0004', 'name' => 'Ana Reyes'],
            ['student_id' => '2024-0005', 'name' => 'Pedro Garcia'],
        ];

        foreach ($seeds as $row) {
            if (DB::table('students')->where('student_id', $row['student_id'])->exists()) {
                continue;
            }
            DB::table('students')->insert([
                'student_id' => $row['student_id'],
                'name' => $row['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('queues') && Schema::hasColumn('queues', 'student_id')) {
            Schema::table('queues', function (Blueprint $table) {
                $table->dropIndex('queues_student_id_index');
                $table->dropColumn('student_id');
            });
        }

        DB::statement("ALTER TABLE queues MODIFY status ENUM('waiting','serving','done','cancelled') DEFAULT 'waiting'");

        Schema::dropIfExists('students');
    }
};
