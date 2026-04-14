<?php

namespace Modules\EnrollmentContracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Students\Models\Student;
use Modules\PaymentInstallments\Models\PaymentInstallment;
use Modules\Payments\Models\Payment;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EnrollmentContract extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'enrollment_contracts'; 

    protected $fillable = [
        'student_id',
        'total_amount_usd',
        'discount_percentage',
        'final_amount_usd',
        'paid_amount_usd',
        'exchange_rate_at_enrollment',
        'final_amount_syp',
        'agreed_at',
        'description',
        'is_active',
        'mode',
        'installments_count',
        'discount_reason',
        'discount_amount',
        'installments_start_date',
    ];

    protected $casts = [
        'installments_start_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships (لم تُحذف أي شيء من كودك)
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function paymentInstallments()
    {
        return $this->hasMany(PaymentInstallment::class, 'enrollment_contract_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'enrollment_contract_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Encryption Mutators + Hash
    |--------------------------------------------------------------------------
    */

    public function setTotalAmountUsdAttribute($value)
    {
        $this->setEncryptedField('total_amount_usd', $value);
    }

    public function getTotalAmountUsdAttribute($value)
    {
        return $this->decryptFloat($value, 'total_amount_usd');
    }

    public function setFinalAmountUsdAttribute($value)
    {
        $this->setEncryptedField('final_amount_usd', $value);
    }

    public function getFinalAmountUsdAttribute($value)
    {
        return $this->decryptFloat($value, 'final_amount_usd');
    }

    public function setPaidAmountUsdAttribute($value)
    {
        $this->setEncryptedField('paid_amount_usd', $value);
    }

    public function getPaidAmountUsdAttribute($value)
    {
        return $this->decryptFloat($value, 'paid_amount_usd');
    }

    public function setFinalAmountSypAttribute($value)
    {
        $this->setEncryptedField('final_amount_syp', $value);
    }

    public function getFinalAmountSypAttribute($value)
    {
        return $this->decryptFloat($value, 'final_amount_syp');
    }

    // ✅ تشفير حقل نسبة الحسم
    public function setDiscountPercentageAttribute($value)
    {
        $this->setEncryptedField('discount_percentage', $value);
    }

    public function getDiscountPercentageAttribute($value)
    {
        return $this->decryptFloat($value, 'discount_percentage');
    }

    // ✅ تشفير حقل مبلغ الحسم
    public function setDiscountAmountAttribute($value)
    {
        $this->setEncryptedField('discount_amount', $value);
    }

    public function getDiscountAmountAttribute($value)
    {
        return $this->decryptFloat($value, 'discount_amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Method
    |--------------------------------------------------------------------------
    */

    protected function setEncryptedField($field, $value)
    {
        if ($value !== null) {
            $this->attributes[$field] = Crypt::encryptString($value);
            $this->attributes["{$field}_hash"] = sha1($value);
        } else {
            $this->attributes[$field] = null;
            $this->attributes["{$field}_hash"] = null;
        }
    }

    protected function decryptFloat($value, $field)
    {
        if (!$value) return null;

        try {
            return (float) Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning("Decryption failed for {$field}", [
                'contract_id' => $this->id,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
}
