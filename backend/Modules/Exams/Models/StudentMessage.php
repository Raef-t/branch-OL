<?php

namespace Modules\Exams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class StudentMessage extends Model implements Auditable 
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'student_id',
        'template_id',
        'status',
    ];
}
