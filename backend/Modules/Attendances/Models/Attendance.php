<?php

namespace Modules\Attendances\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Batches\Models\Batch;
use Modules\InstituteBranches\Models\InstituteBranch;
use Modules\Students\Models\Student;
use Modules\Users\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

use OwenIt\Auditing\Auditable as AuditableTrait;

class Attendance extends Model implements Auditable
{
    use HasFactory,AuditableTrait;

    protected $fillable = [
        'institute_branch_id',
        'student_id',
        'batch_id',
        'attendance_date',
        'status',
        'recorded_by',
        'device_id',
        'recorded_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'recorded_at'     => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(InstituteBranch::class, 'institute_branch_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
