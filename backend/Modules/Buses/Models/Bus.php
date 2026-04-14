<?php

namespace Modules\Buses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Students\Models\Student;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;


use App\Models\Concerns\RestrictDeletion;

class Bus extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'الحافلة';

    protected array $deletionRestrictedRelations = [
        'students' => 'الطلاب المسجلين في هذه الحافلة',
    ];
    
    protected $table = 'buses';

    protected $fillable = [
        'name',
        'capacity',
        'driver_name',
        'route_description',
        'is_active',
    ];

    public $timestamps = true;

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function students()
{
    return $this->hasMany(Student::class, 'bus_id');
}

}
