<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueCall extends Model
{
    protected $table = 'queue_calls';

    protected $fillable = [
        'queue_id',
        'window_id',
        'called_time',
        'finished_time',
    ];

    public $timestamps = false;
}

