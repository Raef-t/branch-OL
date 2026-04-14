<?php

namespace Modules\ExamTypes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Exams\Models\Exam;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\Concerns\RestrictDeletion;

class ExamType extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'نوع الامتحان';

    protected array $deletionRestrictedRelations = [
        'exams' => 'الامتحانات المسجلة بهذا النوع',
    ];

    protected $fillable = [
        'name',
        'description',
    ];

    // علاقة مع الامتحانات
    public function exams()
    {
        return $this->hasMany(Exam::class, 'exam_type_id', 'id');
    }
}
