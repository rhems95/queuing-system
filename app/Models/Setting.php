<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';

    public $timestamps = false;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'updated_at',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        if (! Schema::hasTable('settings')) {
            return $default;
        }

        $value = static::query()->where('setting_key', $key)->value('setting_value');

        return $value !== null ? (string) $value : $default;
    }

    public static function setValue(string $key, string $value): void
    {
        static::query()->updateOrInsert(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'updated_at' => now(),
            ]
        );
    }
}
