<?php

namespace Modules\InstituteBranches\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\Instructors\Models\Instructor;
use Modules\Students\Models\Student;
// use Modules\InstituteBranches\Database\Factories\InstituteBranchFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Concerns\RestrictDeletion;

class InstituteBranch extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'الفرع الرئيسي';

    protected array $deletionRestrictedRelations = [
        'students' => 'الطلاب',
        'classRooms' => 'القاعات التدريسية',
        'instructors' => 'المدربون',
        'employees' => 'الموظفون',
    ];

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'address',
        'code',
        'country_code',
        'phone',
        'email',
        'manager_name',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class,'institute_branch_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'institute_branch_id');
    }
    // public function instructors()
    // {
    //     return $this->belongsToMany(
    //         Instructor::class,
    //         'instructor_institute_branch',
    //         'institute_branch_id',
    //         'instructor_id'
    //     )->withTimestamps();
    // }
    public function instructors()
    {
        return $this->hasMany(
            Instructor::class,
            'institute_branch_id'
        );
    }

    public function employees()
    {
        return $this->hasMany(\Modules\Employees\Models\Employee::class, 'institute_branch_id');
    }
}
