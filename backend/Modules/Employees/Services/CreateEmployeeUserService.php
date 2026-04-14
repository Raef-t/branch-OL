<?php

namespace Modules\Employees\Services;

use Modules\Users\Models\User;
use Modules\Employees\Models\Employee;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

// 'password' => 'Pass1234', // كلمة مرور مؤقتة
class CreateEmployeeUserService
{
    /**
     * إنشاء مستخدم من نوع "employee" لموظف موجود.
     *
     * @throws \DomainException إذا كان الموظف مرتبطًا بمستخدم مسبقًا
     * @throws \RuntimeException إذا فشل توليد معرف فريد
     */
    public function createForEmployee(Employee $employee): User
    {

        if (!$employee instanceof \Illuminate\Database\Eloquent\Model) {
            throw new \RuntimeException('الموظف غير صالح.');
        }


        // 1. التحقق من أن الموظف ليس لديه مستخدم مرتبط
        if ($employee->user) {
            throw new \DomainException('هذا الموظف مرتبط بحساب مستخدم بالفعل.');
        }

        // 2. توليد unique_id فريد: OEM-0000001 إلى OEM-9999999
        $uniqueId = $this->generateUniqueEmployeeId();

        DB::beginTransaction();

        try {
            // 3. إنشاء المستخدم
            $user = User::create([
                'unique_id' => $uniqueId,
                'name' => trim("{$employee->first_name} {$employee->last_name}") ?: "حساب موظف",
                'password' => Hash::make('Pass1234'), // تشفير كلمة المرور
                'is_approved' => true, // ✅ جعله مقبولاً تلقائياً عند التفعيل من قبل المسؤول
                'force_password_change' => true,
            ]);

            // 4. إسناد الدور حسب job_type إن وجد
            if (!empty($employee->job_type)) {
                $roleName = strtolower(trim($employee->job_type));
                if (Role::where('name', $roleName)->exists()) {
                    $user->assignRole($roleName);
                }
            }

            // 5. ربط المستخدم بالموظف بشكل صريح (Force Update)
            $employee->exists = true; // التأكيد على أن السجل موجود
            $employee->update(['user_id' => $user->id]);
            $employee->refresh(); // تأكيد تحميل التغييرات

            DB::commit();

            return $user;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException('حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * توليد معرف فريد بالشكل OEM-XXXXXXX (7 أرقام).
     */
    private function generateUniqueEmployeeId(): string
    {
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $number = random_int(0, 9_999_999);
            $paddedNumber = str_pad($number, 7, '0', STR_PAD_LEFT);
            $uniqueId = "OEM-{$paddedNumber}";

            if (!User::where('unique_id', $uniqueId)->exists()) {
                return $uniqueId;
            }
        }

        throw new \RuntimeException('فشل توليد معرف موظف فريد بعد ' . $maxAttempts . ' محاولة.');
    }
}
