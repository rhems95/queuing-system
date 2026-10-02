<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'card_uid')) {
                $table->string('card_uid', 32)->nullable()->unique()->after('name');
            }
            if (! Schema::hasColumn('students', 'mobile')) {
                $table->string('mobile', 20)->nullable()->after('card_uid');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'card_uid')) {
                $table->dropUnique(['card_uid']);
                $table->dropColumn('card_uid');
            }
            if (Schema::hasColumn('students', 'mobile')) {
                $table->dropColumn('mobile');
            }
        });
    }
};
