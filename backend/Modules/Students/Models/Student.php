<?php

namespace Modules\Students\Models;


use App\Models\Concerns\RestrictDeletion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AcademicBranches\Models\AcademicBranch;
use Modules\AcademicRecords\Models\AcademicRecord;
use Modules\Attendances\Models\Attendance;
use Modules\DoorSessions\Models\DoorSession;
use Modules\Buses\Models\Bus;
use Modules\Cities\Models\City;
use Modules\EnrollmentContracts\Models\EnrollmentContract;
use Modules\ExamResults\Models\ExamResult;
use Modules\Exams\Models\StudentMessage;
use Modules\Families\Models\Family;
use Modules\InstituteBranches\Models\InstituteBranch;
use Modules\StudentStatuses\Models\StudentStatus;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\StudentExits\Models\StudentExitLog;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Modules\Users\Models\User;
use Modules\ContactDetails\Models\ContactDetail;

class Student extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected $fillable = [
        'institute_branch_id',
        'family_id',
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'birth_place',
        'profile_photo_url',
        'id_card_photo_url',
        'branch_id',
        'enrollment_date',
        'start_attendance_date',
        'gender',
        'previous_school_name',
        'national_id',
        'how_know_institute',
        'bus_id',
        'health_status',
        'psychological_status',
        'notes',
        'status_id',
        'city_id',

        'school_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
        'start_attendance_date' => 'date',
    ];

    protected string $deletionRestrictionResource = 'الطالب';

    /**
     * @var array<string, string>
     */
    protected array $deletionRestrictedRelations = [
        'contracts' => 'عقود التسجيل',
        'examResults' => 'نتائج الامتحانات',
        'academicRecords' => 'السجلات الأكاديمية',
        'attendances' => 'سجلات الحضور',
        'batchStudents' => 'تسجيلات الشعب',
        'studentExitLogs' => 'سجلات الانصراف',
        'doorSessions' => 'جلسات البوابة',
        'studentMessages' => 'رسائل الطالب',
        'contactDetails' => 'بيانات الاتصال المرتبطة بالطالب',
    ];

    /**
     * العلاقات التي تعتبر "إدارية" ويمكن حذفها تلقائياً في وضع "الحذف النهائي"
     */
    public array $administrativeRelations = [
        'contactDetails',
        'academicRecords',
        'contracts',
        'studentMessages',
    ];

    /**
     * العلاقات التي تعتبر "تعليمية" وتتطلب تحذيراً شديداً
     */
    public array $educationalRelations = [
        'examResults',
        'attendances',
        'batchStudents',
        'studentExitLogs',
        'doorSessions',
    ];

    // ✅ Accessor لصورة الطالب
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function ($value) {
            if (!$value) {
                return null;
            }

            if (!str_starts_with($value, 'http')) {
                return Storage::url($value);
            }

            return $value;
        });
    }

    // ✅ Accessor لصورة الهوية
    protected function idCardPhotoUrl(): Attribute
    {
        return Attribute::get(function ($value) {
            if (!$value) {
                return null;
            }

            if (!str_starts_with($value, 'http')) {
                return Storage::url($value);
            }

            return $value;
        });
    }

    // =============== mutators للتشفير وإنشاء الهاش ===============


    public function latestAcademicRecord()
    {
        return $this->hasOne(\Modules\AcademicRecords\Models\AcademicRecord::class)
            ->latestOfMany();
    }

    public function father()
    {
        return $this->family?->guardians
            ->firstWhere('relationship', 'father');
    }

    public function mother()
    {
        return $this->family?->guardians
            ->firstWhere('relationship', 'mother');
    }

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
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning('Decryption failed for first_name', ['student_id' => $this->id, 'error' => $e->getMessage()]);
            return null;
        }
    }

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
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning('Decryption failed for last_name', ['student_id' => $this->id, 'error' => $e->getMessage()]);
            return null;
        }
    }

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
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning('Decryption failed for national_id', ['student_id' => $this->id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    // public function latestActiveEnrollmentContract()
    // {
    //     return $this->hasOne(EnrollmentContract::class, 'student_id')
    //         ->where('is_active', 1) 
    //         ->latest('id');
    // }

    public function latestActiveEnrollmentContract()
    {
        return $this->hasOne(EnrollmentContract::class, 'student_id')
            ->where('is_active', true)
            ->latestOfMany();
    }



    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            $first = $this->first_name;
            $last  = $this->last_name;

            if (!$first && !$last) {
                return null;
            }

            return trim("{$first} {$last}");
        });
    }
    public function attendedOnDate($batchId, $date): bool
    {
        return $this->attendances()
            ->where('batch_id', $batchId)
            ->whereDate('attendance_date', $date)
            ->where('status', 'present')
            ->exists();
    }

    // =============== نهاية mutators/accessors ===============

    public function latestAttendance()
    {
        return $this->hasOne(Attendance::class, 'student_id')
            ->latestOfMany();
    }

    public function status()
    {
        return $this->belongsTo(StudentStatus::class, 'status_id');
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function branch()
    {
        return $this->belongsTo(AcademicBranch::class, 'branch_id');
    }

    public function instituteBranch()
    {
        return $this->belongsTo(InstituteBranch::class, 'institute_branch_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function academicRecords()
    {
        return $this->hasMany(AcademicRecord::class);
    }

    public function contracts()
    {
        return $this->hasMany(EnrollmentContract::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function batchStudents()
    {
        return $this->hasMany(BatchStudent::class, 'student_id');
    }

    public function studentExitLogs()
    {
        return $this->hasMany(StudentExitLog::class, 'student_id');
    }

    public function doorSessions()
    {
        return $this->hasMany(DoorSession::class, 'student_id');
    }

    public function studentMessages()
    {
        return $this->hasMany(StudentMessage::class, 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }
    public function latestBatchStudent()
    {
        return $this->hasOne(BatchStudent::class, 'student_id')->latestOfMany();
    }

    public function school()
    {
        return $this->belongsTo(\Modules\Schools\Models\School::class);
    }

    public function contactDetails()
    {
        return $this->hasMany(ContactDetail::class);
    }

    public function batches()
    {
        return $this->belongsToMany(\Modules\Batches\Models\Batch::class, 'batch_student', 'student_id', 'batch_id');
    }
}
