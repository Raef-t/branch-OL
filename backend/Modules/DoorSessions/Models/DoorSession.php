<?php

namespace Modules\DoorSessions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DoorDevices\Models\DoorDevice;
use Modules\Students\Models\Student;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DoorSession extends Model implements Auditable
{
    use HasFactory,AuditableTrait;

    protected $fillable = [
        'device_id',
        'session_token',
        'expires_at',
        'is_used',
        'student_id',
        'used_at',
    ];

    // علاقة مع الجهاز
    public function device()
    {
        return $this->belongsTo(DoorDevice::class, 'device_id');
    }

    // علاقة مع الطالب
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
