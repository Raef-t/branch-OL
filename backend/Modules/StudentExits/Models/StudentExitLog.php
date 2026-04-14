<?php

namespace Modules\StudentExits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Students\Models\Student;
use Modules\Users\Models\User;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class StudentExitLog extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'student_exit_logs';

    protected $fillable = [
        'student_id',
        'exit_date',
        'exit_time',
        'return_time',
        'exit_type',
        'reason',
        'note',
        'recorded_by',
    ];

    protected $casts = [
        'exit_date'  => 'date',
        'exit_time'  => 'datetime:H:i:s',
        'return_time'=> 'datetime:H:i:s',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
