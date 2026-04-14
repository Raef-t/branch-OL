<?php

namespace Modules\Batches\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employees\Models\Employee;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class BatchEmployee extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'batch_employees';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'batch_id',
        'employee_id',
        'role',
        'assigned_by',
        'assignment_date',
        'notes',
        'is_active',
    ];

    /**
     * علاقة: هذا السجل ينتمي إلى دفعة واحدة
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    /**
     * علاقة: هذا السجل ينتمي إلى موظف واحد
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
