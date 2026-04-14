<?php

namespace Modules\Students\Services;

use Modules\Users\Models\User;
use Modules\Students\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateStudentUserService
{
    /**
     * إنشاء مستخدم لطالب وتعيين دور "student" باستخدام Spatie Permissions.
     *
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function createForStudent(Student $student): User
    {
        // 1️⃣ التحقق أن الطالب غير مرتبط بمستخدم مسبقًا
        if ($student->user_id !== null) {
            throw new \DomainException('هذا الطالب مرتبط بحساب مستخدم بالفعل.');
        }

        // 2️⃣ توليد unique_id فريد
        $uniqueId = $this->generateUniqueStudentId();

        return DB::transaction(function () use ($student, $uniqueId) {
            // 3️⃣ إنشاء المستخدم
            $user = User::create([
                'unique_id' => $uniqueId,
                'name' => $student->full_name ?? 'حساب طالب',
                'password' => Hash::make('Pass1234'), // كلمة مرور مشفرة
                'is_approved' => true,
                'force_password_change' => true,
            ]);

            // 4️⃣ تعيين الدور "student" باستخدام Spatie
            $studentRole = Role::where('name', 'student')->first();
            if (! $studentRole) {
                throw new \RuntimeException('دور الطالب (student) غير موجود في جدول roles. تأكد من تشغيل PermissionSeeder.');
            }

            $user->assignRole($studentRole);

            // 5️⃣ ربط المستخدم بالطالب
            $student->user()->associate($user);
            $student->save();

            return $user;
        });
    }

    /**
     * توليد معرف فريد بالشكل OST-XXXXXXX (7 أرقام)
     */
    private function generateUniqueStudentId(): string
    {
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $number = random_int(0, 9_999_999);
            $padded = str_pad($number, 7, '0', STR_PAD_LEFT);
            $uniqueId = "OST-{$padded}";

            if (! User::where('unique_id', $uniqueId)->exists()) {
                return $uniqueId;
            }
        }

        throw new \RuntimeException('فشل توليد معرف فريد بعد عدة محاولات.');
    }
}
