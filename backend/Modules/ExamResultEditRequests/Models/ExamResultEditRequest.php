<?php

namespace Modules\ExamResultEditRequests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ExamResults\Models\ExamResult;
use Modules\Users\Models\User; // افترض أن User في Modules\Users\Models (عدل إذا لزم)
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ExamResultEditRequest extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'exam_result_edit_requests';

    protected $fillable = [
        'exam_result_id',
        'requester_id',
        'original_data',
        'proposed_changes',
        'reason',
        'status',
        'type',
    ];

    protected $casts = [
        'original_data' => 'array', // تحويل JSON إلى array
        'proposed_changes' => 'array', // تحويل JSON إلى array
    ];

    // علاقة مع نتيجة الامتحان
    public function examResult()
    {
        return $this->belongsTo(ExamResult::class, 'exam_result_id');
    }

    // علاقة مع المستخدم الطالب (requester)
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}