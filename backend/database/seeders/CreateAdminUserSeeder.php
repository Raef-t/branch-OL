<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Users\Models\User;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تحقق مما إذا كان يوجد مستخدم admin مسبقًا لتجنب التكرار
        $admin = User::firstOrCreate(
            ['role' => User::ROLE_ADMIN],
            [
                'unique_id'             => 'OAD-00001',
                'name'                  => 'المشرف العام',
                'password'              => 'password123', // سيتم تشفيره تلقائيًا عبر $casts
                'is_approved'           => true,
                'force_password_change' => false,
            ]
        );

        // تعيين دور Spatie للمشرف (يجب أن يكون PermissionSeeder قد شُغِّل أولاً)
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
            $this->command->info("✅ تم إنشاء حساب المشرف وتعيين الدور: {$admin->unique_id}");
        } else {
            $this->command->warn("⚠️  حساب المشرف موجود مسبقًا: {$admin->unique_id}");
        }
    }
}