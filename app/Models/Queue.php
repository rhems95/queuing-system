<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $table = 'queues';

    protected $fillable = [
        'queue_number',
        'service_id',
        'student_id',
        'priority',
        'status',
        'queue_date',
        'issued_by',
        'issue_reason',
    ];

    public $timestamps = false;

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}

