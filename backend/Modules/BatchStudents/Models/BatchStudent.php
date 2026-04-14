<?php

namespace Modules\BatchStudents\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Batches\Models\Batch;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\Students\Models\Student;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\BatchStudentSubjects\Models\BatchStudentSubject;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\Concerns\RestrictDeletion;

class BatchStudent extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'تسجيل الطالب في الشعبة';

    protected array $deletionRestrictedRelations = [
        'batchSubjects' => 'المواد المسجلة للطالب في هذه الشعبة',
        'attendances' => 'سجلات الحضور الخاصة بالطالب في هذه الشعبة',
    ];
    protected $table = 'batch_student';

    protected $fillable = [
        'batch_id',
        'student_id',
        'is_partial',
    ];




    // Modules/BatchStudents/Models/BatchStudent.php

    public function enrollmentType(): string
    {
        return $this->is_partial ? 'partial' : 'full';
    }

    public function effectiveSubjects()
    {
        if ($this->is_partial) {
            return $this->batchSubjects()
                ->wherePivot('status', 'active')
                ->with('subject') // إن وُجد
                ->get();
        }

        // الطالب الكامل لا نُرجع مواد فردية
        return collect();
    }

    // Modules/BatchStudents/Models/BatchStudent.php



    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }


    public function batchSubjects()
    {
        return $this->hasMany(
            BatchStudentSubject::class,
            'batch_student_id'
        );
    }

    public function attendances()
    {
        $batchId = (int) $this->batch_id;

        return $this->hasMany(\Modules\Attendances\Models\Attendance::class, 'student_id', 'student_id')
            ->where('batch_id', $batchId);
    }
}
