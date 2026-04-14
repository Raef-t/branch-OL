<?php

namespace Modules\BatchSubjects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\Batches\Models\Batch;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\ClassSchedules\Models\ClassSchedule;
use Modules\Employees\Models\Employee;
use Modules\Exams\Models\Exam;
use Modules\InstructorSubjects\Models\InstructorSubject;
use Modules\Subjects\Models\Subject;

use App\Models\Concerns\RestrictDeletion;

class BatchSubject extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'المادة ضمن الشعبة';

    protected array $deletionRestrictedRelations = [
        'exams' => 'الامتحانات المرتبطة بهذه المادة في الشعبة',
        'classSchedules' => 'جدول الحصص المرتبط بهذه المادة',
        'partialBatchStudents' => 'الطلاب المسجلين جزئياً في هذه المادة',
    ];
    protected $table = 'batch_subjects';

    protected $fillable = [
        'batch_id',
        'instructor_subject_id',
        'class_room_id',
        'assigned_by',
        'assignment_date',
        'notes',
        'is_active',
        'subject_id',
        'weekly_lessons',
    ];


    /* =======================
     | طلاب الاشتراك الجزئي
     ======================= */

    public function partialBatchStudents()
    {
        return $this->belongsToMany(
            BatchStudent::class,
            'batch_student_subjects',
            'batch_subject_id',
            'batch_student_id'
        )->withPivot('status')->withTimestamps();
    }

    /**
     * الطلاب الفعّالون للمادة:
     * - كل الطلاب غير الجزئيين
     * - + الطلاب الجزئيين المرتبطين بهذه المادة
     */
    public function effectiveBatchStudents()
    {
        return BatchStudent::where('batch_id', $this->batch_id)
            ->where(function ($q) {
                $q->where('is_partial', false)
                    ->orWhereIn(
                        'id',
                        $this->partialBatchStudents()->select('batch_student_id')
                    );
            });
    }
    /* =======================
     | العلاقات الأساسية
     ======================= */

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function instructorSubject()
    {
        return $this->belongsTo(InstructorSubject::class, 'instructor_subject_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    /* =======================
     | الامتحانات والجداول
     ======================= */

    public function exams()
    {
        return $this->hasMany(Exam::class, 'batch_subject_id');
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class, 'batch_subject_id');
    }
}
