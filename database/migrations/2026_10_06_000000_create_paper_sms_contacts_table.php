<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('paper_sms_contacts')) {
            return;
        }

        Schema::create('paper_sms_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('mobile', 20)->unique();
            $table->boolean('enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_sms_contacts');
    }
};
