<?php

namespace Modules\Families\Services;

use Modules\Users\Models\User;
use Modules\Families\Models\Family;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateFamilyUserService
{
    /**
     * إنشاء مستخدم من نوع "family" لعائلة موجودة باستخدام نظام الأدوار الديناميكية (Spatie).
     *
     * @throws \DomainException إذا كانت العائلة غير صالحة أو مرتبطة بمستخدم مسبقًا
     * @throws \RuntimeException إذا فشل توليد معرف فريد أو لم يتم العثور على الدور
     */
    public function createForFamily(Family $family): User
    {
        // 1️⃣ التحقق أن العائلة غير مرتبطة مسبقًا
        if ($family->user_id !== null) {
            throw new \DomainException('هذه العائلة مرتبطة بحساب مستخدم بالفعل.');
        }

        // 2️⃣ التحقق من وجود طالب واحد على الأقل في العائلة
        if (! $family->students()->exists()) {
            throw new \DomainException('لا يمكن إنشاء حساب عائلة بدون وجود طالب.');
        }

        // 3️⃣ توليد unique_id فريد
        $uniqueId = $this->generateUniqueFamilyId();

        return DB::transaction(function () use ($family, $uniqueId) {
            // 4️⃣ إنشاء المستخدم
            $user = User::create([
                'unique_id' => $uniqueId,
                'name' => 'حساب عائلة',
                'password' => Hash::make('Pass1234'), // تشفير كلمة المرور
                'is_approved' => true, // ✅ يتم تفعيله فوراً عند الضغط على زر التفعيل
                'force_password_change' => true,
            ]);

            // 5️⃣ تعيين الدور "family" من Spatie
            $role = Role::where('name', 'parent')
                ->orWhere('name', 'family') // مرونة في الاسم
                ->first();

            if (! $role) {
                throw new \RuntimeException('لم يتم العثور على دور "family" أو "parent" في جدول roles. تأكد من تشغيل PermissionSeeder.');
            }

            $user->assignRole($role);

            // 6️⃣ ربط المستخدم بالعائلة وتفعيل حسابات الطلاب المرتبطين
            $family->user()->associate($user);
            $family->save();

            // ✅ تفعيل جميع حسابات الطلاب لهذه العائلة أو إنشاؤها إذا لم تكن موجودة
            $studentService = new \Modules\Students\Services\CreateStudentUserService();
            foreach ($family->students as $student) {
                if ($student->user_id) {
                    $student->user()->update(['is_approved' => true]);
                } else {
                    try {
                        $studentService->createForStudent($student);
                    } catch (\Exception $e) {
                        // تجاهل الخطأ في حال فشل إنشاء حساب طالب واحد لضمان استمرار العملية
                        \Illuminate\Support\Facades\Log::error("Failed to create student user during family activation: " . $e->getMessage());
                    }
                }
            }

            return $user;
        });
    }

    /**
     * توليد معرف فريد بالشكل OFM-XXXXXXX (7 أرقام)
     */
    private function generateUniqueFamilyId(): string
    {
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $number = random_int(0, 9_999_999);
            $padded = str_pad($number, 7, '0', STR_PAD_LEFT);
            $uniqueId = "OFM-{$padded}";

            if (! User::where('unique_id', $uniqueId)->exists()) {
                return $uniqueId;
            }
        }

        throw new \RuntimeException('فشل توليد معرف عائلة فريد بعد عدة محاولات.');
    }
}
