<?php

namespace Modules\StudentStatuses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Students\Models\Student;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class StudentStatus extends Model implements Auditable
{
    use HasFactory,AuditableTrait;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // في Modules/StudentStatuses/Models/StudentStatus.php

public function students()
{
    return $this->hasMany(Student::class, 'status_id');
}
}