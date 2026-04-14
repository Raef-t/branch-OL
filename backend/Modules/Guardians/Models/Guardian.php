<?php

namespace Modules\Guardians\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ContactDetails\Models\ContactDetail;
use Modules\Families\Models\Family;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

use App\Models\Concerns\RestrictDeletion;

class Guardian extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'ولي الأمر';

    protected array $deletionRestrictedRelations = [
        'contactDetails' => 'بيانات الاتصال المرتبطة بولي الأمر',
    ];

    protected $fillable = [
        'family_id',
        'first_name',      // سيتم تشفيره
        'last_name',       // سيتم تشفيره
        'national_id',     // سيتم تشفيره
        'phone',           // نص عادي
        'is_primary_contact',
        'occupation',
        'address',
        'relationship',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
    ];

    // ========================================================
    // =============== التشفير مع حماية ضد الأخطاء ============
    // ========================================================

    /** ------------------- first_name ------------------- */
    public function setFirstNameAttribute($value)
    {
        $value = ($value !== null) ? trim($value) : null;
        if ($value && strtolower($value) !== 'null' && strtolower($value) !== 'undefined') {
            $this->attributes['first_name'] = Crypt::encryptString($value);
            $this->attributes['first_name_hash'] = sha1($value);
        } else {
            $this->attributes['first_name'] = null;
            $this->attributes['first_name_hash'] = null;
        }
    }

    public function getFirstNameAttribute($value)
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning('Guardian decryption failed', [
                'guardian_id' => $this->id,
                'field' => 'first_name',
                'value' => $value,
            ]);
            return $value; // نرجع النص كما هو بدون توقف
        }
    }

    /** ------------------- last_name ------------------- */
    public function setLastNameAttribute($value)
    {
        $value = ($value !== null) ? trim($value) : null;
        if ($value && strtolower($value) !== 'null' && strtolower($value) !== 'undefined') {
            $this->attributes['last_name'] = Crypt::encryptString($value);
            $this->attributes['last_name_hash'] = sha1($value);
        } else {
            $this->attributes['last_name'] = null;
            $this->attributes['last_name_hash'] = null;
        }
    }

    public function getLastNameAttribute($value)
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning('Guardian decryption failed', [
                'guardian_id' => $this->id,
                'field' => 'last_name',
                'value' => $value,
            ]);
            return $value;
        }
    }

    /** ------------------- national_id ------------------- */
    public function setNationalIdAttribute($value)
    {
        $value = ($value !== null) ? trim($value) : null;
        if ($value && strtolower($value) !== 'null' && strtolower($value) !== 'undefined') {
            $this->attributes['national_id'] = Crypt::encryptString($value);
            $this->attributes['national_id_hash'] = sha1($value);
        } else {
            $this->attributes['national_id'] = null;
            $this->attributes['national_id_hash'] = null;
        }
    }

    public function getNationalIdAttribute($value)
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning('Guardian decryption failed', [
                'guardian_id' => $this->id,
                'field' => 'national_id',
                'value' => $value,
            ]);
            return $value;
        }
    }
    public function primaryPhone()
    {
        return $this->hasOne(ContactDetail::class)
            ->where('type', 'phone')
            ->where('is_primary', true);
    }

    // ========================================================
    // =============== العلاقات ===============================
    // ========================================================

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function contactDetails()
    {
        return $this->hasMany(ContactDetail::class);
    }
}
