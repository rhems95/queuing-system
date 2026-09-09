<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'student_id',
        'name',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
