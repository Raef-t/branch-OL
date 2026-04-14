<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\InstituteBranches\Models\InstituteBranch;
use Modules\Students\Models\Student;
use Modules\EnrollmentContracts\Models\EnrollmentContract;
use Modules\paymentEditRequests\Models\PaymentEditRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class Payment extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'payments';

    protected $fillable = [
        'receipt_number',
        'institute_branch_id',
        'enrollment_contract_id',
        'amount_usd',
        'amount_syp',
        'exchange_rate_at_payment',
        'currency',
        'due_date',
        'paid_date',
        'description',
        'reason',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function instituteBranch()
    {
        return $this->belongsTo(InstituteBranch::class, 'institute_branch_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function enrollmentContract()
    {
        return $this->belongsTo(EnrollmentContract::class, 'enrollment_contract_id');
    }

    public function paymentEditRequests()
    {
        return $this->hasMany(PaymentEditRequest::class, 'payment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Encryption Mutators + Hash
    |--------------------------------------------------------------------------
    */

    public function setAmountUsdAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['amount_usd'] = Crypt::encryptString($value);
            $this->attributes['amount_usd_hash'] = sha1($value);
        } else {
            $this->attributes['amount_usd'] = null;
            $this->attributes['amount_usd_hash'] = null;
        }
    }

    public function getAmountUsdAttribute($value)
    {
        return $this->decryptFloat($value, 'amount_usd');
    }

    public function setAmountSypAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['amount_syp'] = Crypt::encryptString($value);
            $this->attributes['amount_syp_hash'] = sha1($value);
        } else {
            $this->attributes['amount_syp'] = null;
            $this->attributes['amount_syp_hash'] = null;
        }
    }

    public function getAmountSypAttribute($value)
    {
        return $this->decryptFloat($value, 'amount_syp');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Method
    |--------------------------------------------------------------------------
    */

    protected function decryptFloat($value, $field)
    {
        if (!$value) return null;

        try {
            return (float) Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning("Decryption failed for {$field}", [
                'payment_id' => $this->id,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
}
