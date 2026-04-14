<?php

namespace Modules\Schools\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Students\Models\Student;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

use App\Models\Concerns\RestrictDeletion;

class School extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'المدرسة';

    protected array $deletionRestrictedRelations = [
        'students' => 'الطلاب المسجلين في هذه المدرسة',
    ];

    protected $table = 'schools';

    protected $fillable = [
        'name',
        'type',
        'city',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* =======================
     | العلاقات
     ======================= */

    /**
     * المدرسة لديها العديد من الطلاب
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'school_id');
    }
    public function school()
    {
        return $this->belongsTo(\Modules\Schools\Models\School::class, 'school_id');
    }
}
