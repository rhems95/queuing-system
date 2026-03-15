<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Window extends Model
{
    protected $table = 'windows';

    protected $fillable = ['window_name', 'service_id', 'status'];

    public $timestamps = false;

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

