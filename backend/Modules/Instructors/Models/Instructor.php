<?php

namespace Modules\Instructors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\InstituteBranches\Models\InstituteBranch;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\InstructorSubjects\Models\InstructorSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\Concerns\RestrictDeletion;

class Instructor extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'الأستاذ/المدرب';

    protected array $deletionRestrictedRelations = [
        'instructorSubjects' => 'المواد الدراسية المسندة',
    ];

    protected $table = 'instructors';

    protected $fillable = [
        'user_id',
        'institute_branch_id', // ⭐ مهم
        'name',
        'phone',
        'specialization',
        'hire_date',
        'profile_photo_url',
        'preferences',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];
    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            $first = $this->first_name ?? '';
            $last  = $this->last_name ?? '';
            return trim("{$first} {$last}") ?: null;
        });
    }
    public function instituteBranch()
    {
        return $this->belongsTo(
            InstituteBranch::class,
            'institute_branch_id'
        );
    }

    public function instructorSubjects()
    {
        return $this->hasMany(InstructorSubject::class, 'instructor_id', 'id');
    }
}
