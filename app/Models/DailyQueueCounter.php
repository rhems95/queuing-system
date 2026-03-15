<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyQueueCounter extends Model
{
    protected $table = 'daily_queue_counters';

    protected $fillable = ['service_id', 'queue_date', 'last_number'];

    public $timestamps = false;
}

