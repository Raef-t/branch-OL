<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // نرث من Authenticatable مشان نقدر نستخدمه بالمصادقة
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\FcmTokens\Models\FcmToken;
use Modules\NotificationRecipients\Models\NotificationRecipient;

use App\Models\Concerns\RestrictDeletion;

class User extends Authenticatable implements Auditable
{
    use HasFactory, Notifiable, AuditableTrait, HasApiTokens, HasRoles, RestrictDeletion;

    protected string $deletionRestrictionResource = 'المستخدم';

    protected array $deletionRestrictedRelations = [
        'student' => 'سجل الطالب المرتبط بالحساب',
        'employee' => 'سجل الموظف المرتبط بالحساب',
        'instructor' => 'سجل المدرس المرتبط بالحساب',
        'family' => 'سجل العائلة المرتبط بالحساب',
    ];

    // اسم الجدول   
    protected $table = 'users';
    protected $guard_name = 'sanctum';
    // الأعمدة المسموح تعبئتها
    protected $fillable = [
        'unique_id',
        'name',
        'password',
        'role',
        'is_approved',
        'force_password_change'
    ];

    // الأعمدة المخفية عند التحويل إلى JSON
    protected $hidden = [
        'password',
        'remember_token', // لو أضفت لاحقًا
    ];

    // التحويل التلقائي لأنواع البيانات
    protected $casts = [
        'password' => 'hashed',
        'is_approved' => 'boolean',
        'force_password_change' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // أدوار النظام (ممكن تحتاجها كثوابت)
    public const ROLE_ADMIN   = 'admin';
    public const ROLE_EMPLOYEE   = 'employee';
    public const ROLE_STUDENT = 'student';
    public const ROLE_FAMILY  = 'family';

    public function getUserTypeAttribute(): ?string
    {
        if ($this->employee()->exists()) return 'employee';
        if ($this->teacher()->exists()) return 'teacher';
        if ($this->student()->exists()) return 'student';
        if ($this->family()->exists()) return 'family';
        return null;
    }


    public function family()
    {
        return $this->hasOne(\Modules\Families\Models\Family::class);
    }
    public function employee()
    {
        return $this->hasOne(\Modules\Employees\Models\Employee::class);
    }
    public function student()
    {
        return $this->hasOne(\Modules\Students\Models\Student::class);
    }

    // علاقة مع التوكنات: يمكن لكل يوزر أن يكون لديه عدة توكنات FCM (hasMany)
    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }
    public function instructor()
    {
        return $this->hasOne(\Modules\Instructors\Models\Instructor::class);
    }

    public function notifications()
    {
        return $this->hasMany(NotificationRecipient::class, 'user_id');
    }
}
