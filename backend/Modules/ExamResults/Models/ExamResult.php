<?php

namespace Modules\ExamResults\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ExamResultEditRequests\Models\ExamResultEditRequest;
use Modules\Exams\Models\Exam;
use Modules\Students\Models\Student;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ExamResult extends Model implements Auditable
{
    use HasFactory, AuditableTrait;
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'obtained_marks',
        'is_passed',
        'remarks',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    // علاقة مع الطالب
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function editRequests()
    {
        return $this->hasMany(ExamResultEditRequest::class, 'exam_result_id');
    }
}
