<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('queues') || Schema::hasColumn('queues', 'screen_token')) {
            return;
        }

        Schema::table('queues', function (Blueprint $table) {
            $table->string('screen_token', 12)->nullable()->after('issue_reason');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('queues') || ! Schema::hasColumn('queues', 'screen_token')) {
            return;
        }

        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn('screen_token');
        });
    }
};
