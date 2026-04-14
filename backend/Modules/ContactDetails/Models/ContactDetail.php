<?php

namespace Modules\ContactDetails\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\Guardians\Models\Guardian;
use Modules\Students\Models\Student;
use Modules\Families\Models\Family;

class ContactDetail extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    // 🟢 مصفوفة مؤقتة (لا تحفظ في قاعدة البيانات) لحمل أرقام الهواتف التي تم نزع الأساسي عنها
    public array $oldPrimaryNumbersReplaced = [];

    protected $fillable = [
        'guardian_id',
        'student_id',
        'family_id',
        'type',
        'value',
        'country_code',
        'phone_number',
        'owner_type',
        'owner_name',
        'supports_call',
        'supports_whatsapp',
        'supports_sms',
        'is_primary',
        'is_sms_stopped',
        'stop_sms_from',
        'stop_sms_to',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'supports_call' => 'boolean',
        'supports_whatsapp' => 'boolean',
        'supports_sms' => 'boolean',
        'is_sms_stopped' => 'boolean',
        'stop_sms_from' => 'date',
        'stop_sms_to' => 'date',
    ];

    /**
     * القيم الافتراضية التلقائية عند إنشاء/تحديث سجل من نوع landline:
     * - supports_whatsapp = false (الأرضي لا يدعم واتساب)
     * - supports_sms      = false (الأرضي لا يدعم SMS)
     * - supports_call     = true  (الأرضي الغرض الأساسي منه الاتصال)
     * - owner_type        = family (الأرضي لعائلة بأكملها وليس شخصًا)
     */
    protected static function boot()
    {
        parent::boot();

        $applyLandlineDefaults = function ($model) {
            if ($model->type === 'landline') {
                $model->supports_whatsapp = false;
                $model->supports_sms      = false;
                $model->supports_call     = true;
                $model->is_primary        = false; // 🚫 الأرضي لا يمكن أن يكون أساسياً

                // تعيين owner_type = family إذا لم يُحدَّد
                if (empty($model->owner_type)) {
                    $model->owner_type = 'family';
                }

                // مسح guardian_id و student_id لأن الأرضي يرتبط بالعائلة
                // (في حال كان هناك ربط خاطئ)
                $model->guardian_id = null;
                $model->student_id  = null;
            }
        };

        static::creating($applyLandlineDefaults);
        static::updating($applyLandlineDefaults);

        // 🟢 إدارة الرقم الأساسي ومزامنة الـ SMS (قبل الحفظ) - صرامة مطلقة
        $handlePrimaryAndSms = function ($model) {
            // 1. مزامنة إجبارية ثنائية الاتجاه
            if ($model->isDirty('supports_sms')) {
                $model->is_primary = (bool) $model->supports_sms;
            }
            if ($model->isDirty('is_primary')) {
                $model->supports_sms = (bool) $model->is_primary;
            }

            // 2. إذا أصبح هذا السجل أساسياً، يجب تنظيف النطاق بالكامل
            if ($model->is_primary) {
                $resolvedFamilyId = $model->family_id;
                $guardianId = $model->guardian_id;
                $studentId = $model->student_id;

                // محاولة إيجاد family_id إذا كان مفقوداً
                if (!$resolvedFamilyId) {
                    if ($guardianId) {
                        $guardian = \Modules\Guardians\Models\Guardian::find($guardianId);
                        $resolvedFamilyId = $guardian?->family_id;
                    } elseif ($studentId) {
                        $student = \Modules\Students\Models\Student::find($studentId);
                        $resolvedFamilyId = $student?->family_id;
                    }
                }

                // تحديد المعرفات المرتبطة بالعائلة لضمان التنظيف الشامل
                $familyGuardianIds = [];
                $familyStudentIds = [];
                if ($resolvedFamilyId) {
                    $familyGuardianIds = \Modules\Guardians\Models\Guardian::where('family_id', $resolvedFamilyId)->pluck('id')->toArray();
                    $familyStudentIds = \Modules\Students\Models\Student::where('family_id', $resolvedFamilyId)->pluck('id')->toArray();
                }

                // بناء استعلام التنظيف (كل ما يخص هذه العائلة أو هؤلاء الأشخاص)
                $oldPrimaryQuery = self::where('id', '!=', $model->id ?? 0)
                    ->where('is_primary', true)
                    ->where(function ($query) use ($resolvedFamilyId, $guardianId, $studentId, $familyGuardianIds, $familyStudentIds) {
                        if ($resolvedFamilyId) $query->where('family_id', $resolvedFamilyId);
                        if ($guardianId) $query->orWhere('guardian_id', $guardianId);
                        if ($studentId) $query->orWhere('student_id', $studentId);
                        if (!empty($familyGuardianIds)) $query->orWhereIn('guardian_id', $familyGuardianIds);
                        if (!empty($familyStudentIds)) $query->orWhereIn('student_id', $familyStudentIds);
                    });

                // تنفيذ التنظيف القسري
                $oldPrimaryQuery->update([
                    'is_primary' => false,
                    'supports_sms' => false
                ]);
            }
        };

        static::saving($handlePrimaryAndSms);
    }

    // العلاقة
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    // ✨ خاصية مخصصة لعرض الرقم الكامل (للأرقام فقط)
    public function getFullPhoneNumberAttribute()
    {
        if (!in_array($this->type, ['phone', 'landline'])) {
            return null;
        }

        if ($this->country_code && $this->phone_number) {
            return $this->country_code . $this->phone_number;
        }

        return $this->value;
    }

    // ✨ خاصية: هل هذا هاتف أرضي؟
    public function getIsLandlineAttribute(): bool
    {
        return $this->type === 'landline';
    }
}
