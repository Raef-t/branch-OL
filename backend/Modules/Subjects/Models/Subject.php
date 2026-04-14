<?php

namespace Modules\Subjects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\InstructorSubjects\Models\InstructorSubject;
use Modules\AcademicBranches\Models\AcademicBranch; // استدعاء الموديول
use Modules\BatchSubjects\Models\BatchSubject;

use App\Models\Concerns\RestrictDeletion;

class Subject extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'المادة الدراسية';

    protected array $deletionRestrictedRelations = [
        'instructorSubjects' => 'المدرسين المسندين للمادة',
        'batchSubjects' => 'الشعب الدراسية التي تدرس المادة',
    ];

    protected $table = 'subjects';

    protected $fillable = [
        'name',
        'description',
        'academic_branch_id', // إضافة العمود الجديد للملء الجماعي
    ];

    public function instructorSubjects()
    {
        return $this->hasMany(InstructorSubject::class, 'subject_id', 'id');
    }

    // الربط مع AcademicBranch
    public function academicBranch()
    {
        return $this->belongsTo(AcademicBranch::class, 'academic_branch_id');
    }


    public function batchSubjects()
    {
        return $this->hasMany(BatchSubject::class, 'subject_id', 'id');
    }
}
