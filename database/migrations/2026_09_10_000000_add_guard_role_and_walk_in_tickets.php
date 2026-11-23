<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','guard') NOT NULL");

        Schema::table('queues', function (Blueprint $table) {
            if (! Schema::hasColumn('queues', 'issued_by')) {
                $table->unsignedBigInteger('issued_by')->nullable()->after('student_id');
                $table->index('issued_by');
            }
            if (! Schema::hasColumn('queues', 'issue_reason')) {
                $table->string('issue_reason', 32)->nullable()->after('issued_by');
            }
        });

        $exists = DB::table('users')->where('email', 'guard@gmail.com')->exists();
        if (! $exists) {
            $row = [
                'name' => 'Guard',
                'email' => 'guard@gmail.com',
                'password' => Hash::make('guard'),
                'role' => 'guard',
                'window_id' => null,
            ];
            if (Schema::hasColumn('users', 'created_at')) {
                $row['created_at'] = now();
            }
            if (Schema::hasColumn('users', 'updated_at')) {
                $row['updated_at'] = now();
            }
            DB::table('users')->insert($row);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'guard@gmail.com')->where('role', 'guard')->delete();

        Schema::table('queues', function (Blueprint $table) {
            if (Schema::hasColumn('queues', 'issued_by')) {
                $table->dropIndex(['issued_by']);
                $table->dropColumn('issued_by');
            }
            if (Schema::hasColumn('queues', 'issue_reason')) {
                $table->dropColumn('issue_reason');
            }
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff') NOT NULL");
    }
};
