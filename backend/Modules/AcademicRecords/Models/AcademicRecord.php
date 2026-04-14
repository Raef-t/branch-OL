<?php

namespace Modules\AcademicRecords\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Students\Models\Student;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class AcademicRecord extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'student_id',
        'record_type',
        'total_score',  
        'year',
        'description',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}