<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('queues')) {
            return;
        }

        Schema::table('queues', function (Blueprint $table) {
            if (! Schema::hasColumn('queues', 'sms_mobile')) {
                $table->string('sms_mobile', 20)->nullable()->after('issue_reason');
            }
            if (! Schema::hasColumn('queues', 'sms_issued')) {
                $table->unsignedTinyInteger('sms_issued')->default(0)->after('sms_mobile');
            }
            if (! Schema::hasColumn('queues', 'sms_near')) {
                $table->unsignedTinyInteger('sms_near')->default(0)->after('sms_issued');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('queues')) {
            return;
        }

        Schema::table('queues', function (Blueprint $table) {
            foreach (['sms_near', 'sms_issued', 'sms_mobile'] as $column) {
                if (Schema::hasColumn('queues', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
