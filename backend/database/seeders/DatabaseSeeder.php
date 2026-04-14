<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. الأدوار والصلاحيات أولاً (شرط لتعيين الأدوار للمستخدمين)
        $this->call(PermissionSeeder::class);

        // 2. حساب Admin الثابت + تعيين دور Spatie
        $this->call(CreateAdminUserSeeder::class);

        // 3. الإعدادات الأساسية للنظام
        $this->call(AAASettingsFixSeeder::class);

        // 4. البيانات الأساسية للموديولات
        $this->call(\Modules\Cities\Database\Seeders\CitiesTableSeeder::class);
        $this->call(\Modules\ExamTypes\Database\Seeders\ExamTypesTableSeeder::class);
        $this->call(\Modules\ClassRooms\Database\Seeders\ClassRoomsTableSeeder::class);
        $this->call(\Modules\Buses\Database\Seeders\BusesTableSeeder::class);
        $this->call(\Modules\AcademicBranches\Database\Seeders\AcademicBranchesTableSeeder::class);
        $this->call(\Modules\InstituteBranches\Database\Seeders\InstituteBranchesTableSeeder::class);
        $this->call(\Modules\Subjects\Database\Seeders\SubjectsTableSeeder::class);
        
        // 5. تهيئة أجهزة الأبواب (بيانات مطابقة للسيرفر الحالي)
        $this->call(\Modules\DoorDevices\Database\Seeders\DoorDevicesDatabaseSeeder::class);
    }
}
