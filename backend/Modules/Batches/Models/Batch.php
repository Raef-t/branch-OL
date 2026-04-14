<?php

namespace Modules\Batches\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\InstituteBranches\Models\InstituteBranch;
use Modules\Batches\Scopes\VisibleBatchScope;
use Modules\Batches\Scopes\NonArchivedScope;

use App\Models\Concerns\RestrictDeletion;

class Batch extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'الشعبة الدراسية';

    protected array $deletionRestrictedRelations = [
        'batchStudents' => 'الطلاب المسجلين',
        'attendances' => 'سجلات الحضور',
        'batchEmployees' => 'الكادر الإداري/التدريسي',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new VisibleBatchScope);
        static::addGlobalScope(new NonArchivedScope);

        // Cascade delete subjects when deleting a batch
        static::deleting(function ($batch) {
            $batch->batchSubjects->each->delete();
        });
    }
    protected $fillable = [
        'institute_branch_id',
        'academic_branch_id',
        'class_room_id',
        'name',
        'start_date',
        'end_date',
        'is_archived',
        'is_hidden',
        'is_completed',
        'gender_type',
    ];

    /* ============================================================
     * Scopes
     * ============================================================ */

    public function scopeForGender($query, $gender)
    {
        if (!$gender) {
            return $query;
        }

        $gender = strtolower($gender);

        if (!in_array($gender, ['male', 'female', 'mixed'])) {
            return $query;
        }

        return $query->where('gender_type', $gender);
    }

    /**
     * فلترة حسب اسم الدورة (بحث جزئي)
     */
    public function scopeFilterByName($query, $name)
    {
        if (!$name) return $query;
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * فلترة حسب اسم طالب مسجل في الدورة
     */
    public function scopeFilterByStudentName($query, $studentName)
    {
        if (!$studentName) return $query;

        return $query->whereHas('batchStudents.student', function ($q) use ($studentName) {
            $q->where('first_name', 'like', "%{$studentName}%")
              ->orWhere('last_name', 'like', "%{$studentName}%");
        });
    }

    /**
     * فلترة حسب الحالة: active, completed, archived, hidden
     */
    public function scopeFilterByStatus($query, $status)
    {
        if (!$status) return $query;

        return match ($status) {
            'active'    => $query->where('is_archived', false)
                                 ->where('is_hidden', false),

            'completed' => $query->where('is_completed', true),

            'full'      => $query->where('is_completed', true),

            'archived'  => $query->withoutGlobalScope(NonArchivedScope::class)
                                 ->where('is_archived', true),

            'hidden'    => $query->withoutGlobalScope(VisibleBatchScope::class)
                                 ->where('is_hidden', true),

            default     => $query,
        };
    }

    /* ============================================================
     * Relationships
     * ============================================================ */

    public function batchSubjects()
    {
        return $this->hasMany(\Modules\BatchSubjects\Models\BatchSubject::class, 'batch_id');
    }

    public function academicBranch()
    {
        return $this->belongsTo(\Modules\AcademicBranches\Models\AcademicBranch::class, 'academic_branch_id');
    }

    public function batchEmployees()
    {
        return $this->hasMany(BatchEmployee::class, 'batch_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function instituteBranch()
    {
        return $this->belongsTo(
            InstituteBranch::class,
            'institute_branch_id'
        );
    }

    public function batchStudents()
    {
        return $this->hasMany(BatchStudent::class, 'batch_id');
    }

    public function attendances()
    {
        return $this->hasMany(\Modules\Attendances\Models\Attendance::class, 'batch_id');
    }
}
