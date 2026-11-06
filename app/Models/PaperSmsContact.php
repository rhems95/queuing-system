<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperSmsContact extends Model
{
    protected $table = 'paper_sms_contacts';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'mobile',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function asLocalMobile(string $stored): string
    {
        $digits = preg_replace('/\D+/', '', $stored) ?? '';
        if (str_starts_with($digits, '63') && strlen($digits) === 12) {
            return '0'.substr($digits, 2);
        }

        return $stored;
    }
}
