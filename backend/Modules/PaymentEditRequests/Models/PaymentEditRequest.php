<?php
// app/Models/PaymentEditRequest.php (أو في Modules\Payments\Models)

namespace Modules\PaymentEditRequests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payments\Models\Payment;
use Modules\Users\Models\User;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;; // افترض User model

class PaymentEditRequest extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'payment_edit_requests';

    protected $fillable = [
        'payment_id',
        'requester_id',
        'original_data',
        'proposed_changes',
        'reason',
        'status',
        'reviewer_comment',
        'reviewer_id',
        'action',
    ];

    protected $casts = [
        'original_data' => 'array',
        'proposed_changes' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}