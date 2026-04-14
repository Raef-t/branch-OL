<?php

namespace Modules\ClassSchedules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\ClassRooms\Models\ClassRoom;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ClassSchedule extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'class_schedules';

    protected $fillable = [
        'batch_subject_id',
        'day_of_week',



        'period_number',

        'start_time',
        'end_time',


        'class_room_id',

        'is_default',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',

    ];

    /* =======================
     | العلاقات
     ======================= */

    public function batchSubject()
    {
        return $this->belongsTo(BatchSubject::class, 'batch_subject_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }
}
