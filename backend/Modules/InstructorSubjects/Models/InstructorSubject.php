<?php

namespace Modules\InstructorSubjects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class InstructorSubject extends Model implements Auditable
{
    use HasFactory,AuditableTrait;

    protected $table = 'instructor_subjects';

    protected $fillable = [
        'instructor_id',
        'subject_id',
        'is_active',
    ];

    // علاقات
    public function instructor()
    {
        return $this->belongsTo(\Modules\Instructors\Models\Instructor::class, 'instructor_id');
    }

    public function subject()
    {
        return $this->belongsTo(\Modules\Subjects\Models\Subject::class, 'subject_id');
    }

    
    public function batchSubjects()
    {
        return $this->hasMany(\Modules\BatchSubjects\Models\BatchSubject::class, 'instructor_subject_id');
    }
}