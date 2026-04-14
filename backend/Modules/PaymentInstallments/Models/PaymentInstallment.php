<?php

namespace Modules\PaymentInstallments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\EnrollmentContracts\Models\EnrollmentContract;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class PaymentInstallment extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'payment_installments';

    protected $fillable = [
        'enrollment_contract_id',
        'installment_number',
        'due_date',
        'planned_amount_usd',
        'exchange_rate_at_due_date',
        'planned_amount_syp',
        'status',
        'paid_amount_usd',
    ];

    // علاقة مع العقد
    public function enrollmentContract()
    {
        return $this->belongsTo(EnrollmentContract::class, 'enrollment_contract_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Encryption Mutators + Hash
    |--------------------------------------------------------------------------
    */

    public function setPlannedAmountUsdAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['planned_amount_usd'] = Crypt::encryptString($value);
            $this->attributes['planned_amount_usd_hash'] = sha1($value);
        } else {
            $this->attributes['planned_amount_usd'] = null;
            $this->attributes['planned_amount_usd_hash'] = null;
        }
    }

    public function getPlannedAmountUsdAttribute($value)
    {
        return $this->decryptFloat($value, 'planned_amount_usd');
    }

    public function setPlannedAmountSypAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['planned_amount_syp'] = Crypt::encryptString($value);
            $this->attributes['planned_amount_syp_hash'] = sha1($value);
        } else {
            $this->attributes['planned_amount_syp'] = null;
            $this->attributes['planned_amount_syp_hash'] = null;
        }
    }

    public function getPlannedAmountSypAttribute($value)
    {
        return $this->decryptFloat($value, 'planned_amount_syp');
    }

    public function setPaidAmountUsdAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['paid_amount_usd'] = Crypt::encryptString($value);
            $this->attributes['paid_amount_usd_hash'] = sha1($value);
        } else {
            $this->attributes['paid_amount_usd'] = null;
            $this->attributes['paid_amount_usd_hash'] = null;
        }
    }

    public function getPaidAmountUsdAttribute($value)
    {
        return $this->decryptFloat($value, 'paid_amount_usd');
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
                'installment_id' => $this->id,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
}
