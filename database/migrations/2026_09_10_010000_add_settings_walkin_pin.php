<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('setting_key', 64)->unique();
                $table->string('setting_value', 255);
                $table->timestamp('updated_at')->nullable();
            });
        }

        $exists = DB::table('settings')->where('setting_key', 'walkin_pin')->exists();
        if (! $exists) {
            DB::table('settings')->insert([
                'setting_key' => 'walkin_pin',
                'setting_value' => '1981',
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
