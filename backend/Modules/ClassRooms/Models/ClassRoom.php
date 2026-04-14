<?php

namespace Modules\ClassRooms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Batches\Models\Batch;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\InstituteBranches\Models\InstituteBranch;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\Concerns\RestrictDeletion;

class ClassRoom extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'القاعة الدراسية';

    protected array $deletionRestrictedRelations = [
        'batches' => 'الشعب الدراسية المسندة للقاعة',
        'batchStudents' => 'الطلاب المتواجدين حالياً في القاعة',
        'batchSubjects' => 'المواد الدراسية المرتبطة بالقاعة',
        'classSchedules' => 'جدول الحصص المرتبط بالقاعة',
    ];

    protected $table = 'class_rooms';

    protected $fillable = [
        'name',
        'code',
        'capacity',
        'notes',
        'institute_branch_id'
    ];

    public function instituteBranch()
    {
        return $this->belongsTo(InstituteBranch::class,'institute_branch_id');
    }
    
    public function batchStudents()
    {
        return $this->hasMany(BatchStudent::class);
    }

    public function batchSubjects()
    {
        return $this->hasMany(BatchSubject::class, 'class_room_id');
    }
    public function batches()
    {
        return $this->hasMany(Batch::class, 'class_room_id');
    }
    public function classSchedules()
    {
        return $this->hasMany(
            \Modules\ClassSchedules\Models\ClassSchedule::class,
            'class_room_id'
        );
    }
}
