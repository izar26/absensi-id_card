<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    // Tambahkan ini
    protected $fillable = [
        'session_date',
        'start_time',
        'end_time',
        'description',
    ];
}