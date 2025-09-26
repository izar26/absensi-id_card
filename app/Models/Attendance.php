<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'status',
    ];

    /**
     * Mendefinisikan relasi bahwa setiap catatan absensi
     * "milik" satu siswa (belongsTo).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}