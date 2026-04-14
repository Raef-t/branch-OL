<?php

namespace Modules\Families\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Guardians\Models\Guardian;
use Modules\Students\Models\Student;
use Modules\Users\Models\User;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Modules\ContactDetails\Models\ContactDetail;

// use Modules\Families\Database\Factories\FamilyFactory;

use App\Models\Concerns\RestrictDeletion;

class Family extends Model implements Auditable
{
    use HasFactory, AuditableTrait, RestrictDeletion;

    protected string $deletionRestrictionResource = 'العائلة';

    protected array $deletionRestrictedRelations = [
        'students' => 'الطلاب التابعين للعائلة',
        'guardians' => 'أولياء الأمور المرتبطين بالعائلة',
        'contactDetails' => 'بيانات الاتصال المرتبطة بالعائلة',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function contactDetails()
    {
        return $this->hasMany(ContactDetail::class);
    }
}
