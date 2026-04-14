<?php

namespace Modules\BatchStudentSubjects\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\BatchSubjects\Models\BatchSubject;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
    
class BatchStudentSubject extends Model implements Auditable
{
    use AuditableTrait;
    protected $table = 'batch_student_subjects';

    protected $fillable = [
        'batch_student_id',
        'batch_subject_id',
        'status',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    public function batchStudent()
    {
        return $this->belongsTo(BatchStudent::class);
    }

    public function batchSubject()
    {
        return $this->belongsTo(BatchSubject::class);
    }
}
