<?php

namespace Modules\Cities\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\Students\Models\Student;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\Concerns\RestrictDeletion;

class City extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'المدينة';

    protected array $deletionRestrictedRelations = [
        'students' => 'الطلاب المقيمين في هذه المدينة',
    ];

    protected $table = 'cities'; // اسم الجدول

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];
    
       public function students()
    {
        return $this->hasMany(Student::class);
    }
  
}
