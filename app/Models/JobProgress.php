<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobProgress extends Model
{
    protected $fillable = [
        'job_type',
        'total_batches',
        'completed_batches',
        'percentage_complete',
        'status',
        'file_path',
    ];
}
