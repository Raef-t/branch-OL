<?php

namespace Modules\AcademicBranches\Models;

use App\Models\Concerns\RestrictDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Students\Models\Student;
use Modules\Subjects\Models\Subject;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class AcademicBranch extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected $fillable = [
        'name',
        'description',
    ];

    protected string $deletionRestrictionResource = 'الفرع الأكاديمي';

    /**
     * @var array<string, string>
     */
    protected array $deletionRestrictedRelations = [
        'students' => 'الطلاب',
        'subjects' => 'المواد',
        'batches' => 'الشعب',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'branch_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'academic_branch_id');
    }

    public function batches()
    {
        return $this->hasMany(\Modules\Batches\Models\Batch::class, 'academic_branch_id');
    }
}

