<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // تنظيف كاش الصلاحيات
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | الصلاحيات الأساسية فقط (view / create / update)
        |--------------------------------------------------------------------------
        */
        $modules = [
            'students',
            'batches',
            'subjects',
            'attendances',
            'payments',
            'exams',
            'exam_results',
            'reports',
            'message_templates',
        ];

        $actions = ['view', 'create', 'update'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'sanctum',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | إنشاء الأدوار
        |--------------------------------------------------------------------------
        */
        $roles = [
            'admin',
            'manager',
            'teacher',
            'employee_accountant',
            'employee_data_entry',
            'employee_auditor',
            'student',
            'parent',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'sanctum',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ربط الصلاحيات بالأدوار
        |--------------------------------------------------------------------------
        */

        // 🔴 ADMIN — كل شيء
        Role::where('name', 'admin')
            ->first()
            ->syncPermissions(Permission::all());

        // 🟠 MANAGER — تشغيل عام بدون حذف
        Role::where('name', 'manager')
            ->first()
            ->syncPermissions(Permission::whereIn('name', [
                'students.view',
                'students.create',
                'students.update',

                'batches.view',
                'batches.create',
                'batches.update',

                'subjects.view',

                'attendances.view',
                'reports.view',
            ])->get());

        // 🟢 TEACHER — تدريس فقط
        Role::where('name', 'teacher')
            ->first()
            ->syncPermissions(Permission::whereIn('name', [
                'students.view',
                'subjects.view',
                'attendances.create',
                'attendances.update',
                'exams.view',
                'exam_results.create',
                'exam_results.update',
            ])->get());

        // 🔵 EMPLOYEE (محاسب)
        Role::where('name', 'employee_accountant')
            ->first()
            ->syncPermissions(Permission::whereIn('name', [
                'payments.view',
                'payments.create',
                'reports.view',
            ])->get());

        // 🟣 EMPLOYEE (مدخل بيانات)
        Role::where('name', 'employee_data_entry')
            ->first()
            ->syncPermissions(Permission::whereIn('name', [
                'students.create',
                'students.update',
                'batches.create',
                'batches.update',
            ])->get());

        // 🟤 EMPLOYEE (مدقق)
        Role::where('name', 'employee_auditor')
            ->first()
            ->syncPermissions(Permission::whereIn('name', [
                'students.view',
                'payments.view',
                'reports.view',
            ])->get());

        // ⚪ STUDENT & PARENT — بدون صلاحيات Backend
        Role::whereIn('name', ['student', 'parent'])
            ->get()
            ->each(fn ($role) => $role->syncPermissions([]));

        // إعادة تنظيف الكاش
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('✅ تم إنشاء الأدوار والصلاحيات الأساسية بنجاح');
    }
}
