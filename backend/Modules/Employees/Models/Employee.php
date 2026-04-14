<?php

namespace Modules\Employees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\InstituteBranches\Models\InstituteBranch;
use Modules\Users\Models\User;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\Batches\Models\BatchEmployee;
use Modules\Employees\Scopes\ActiveEmployeeScope;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\Concerns\RestrictDeletion;

class Employee extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'الموظف';

    protected array $deletionRestrictedRelations = [
        'batchAssignments' => 'تكاليف الشعب الدراسية',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ActiveEmployeeScope);
    }
    protected $fillable = [
        'user_id',
        'first_name',
        'institute_branch_id',
        'last_name',
        'job_title',
        'job_type',
        'hire_date',
        'phone',
        'is_active',
        'photo_path',

    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : null;
    }
    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            $first = $this->first_name ?? '';
            $last  = $this->last_name ?? '';
            return trim("{$first} {$last}") ?: null;
        });
    }
    // علاقة مع المستخدم (User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // علاقة مع فرع المعهد (الموقع الجغرافي)
    public function instituteBranch()
    {
        return $this->belongsTo(InstituteBranch::class, 'institute_branch_id');
    }


    public function batchAssignments()
    {
        return $this->hasMany(BatchEmployee::class, 'employee_id');
    }
}
