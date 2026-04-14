<?php

namespace Modules\ClassSchedules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\ClassRooms\Models\ClassRoom;

class ClassScheduleDraft extends Model
{
    use HasFactory;

    protected $table = 'schedule_drafts';

    protected $fillable = [
        'draft_group_id',
        'batch_subject_id',
        'day_of_week',
        'period_number',
        'start_time',
        'end_time',
        'class_room_id',
        'is_conflict',
        'conflict_message',
    ];

    protected $casts = [
        'is_conflict' => 'boolean',
    ];

    public function batchSubject()
    {
        return $this->belongsTo(BatchSubject::class, 'batch_subject_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }
}
