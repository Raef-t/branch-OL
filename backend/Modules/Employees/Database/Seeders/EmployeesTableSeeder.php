<?php

namespace Modules\Employees\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employees\Models\Employee;
use Carbon\Carbon;

class EmployeesTableSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */
        Employee::create([
            'first_name'          => 'عبدالله',
            'last_name'           => 'المدير',
            'job_title'           => 'مدير النظام',
            'job_type'            => 'admin',
            'hire_date'           => Carbon::create(2020, 1, 1),
            'phone'               => '0999000001',
            'is_active'           => true,
            'institute_branch_id' => 1,
            'user_id'             => null,
            'photo_path'          => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Supervisor
        |--------------------------------------------------------------------------
        */
        Employee::create([
            'first_name'          => 'سامي',
            'last_name'           => 'الحسن',
            'job_title'           => 'مشرف إداري',
            'job_type'            => 'supervisor',
            'hire_date'           => Carbon::create(2021, 5, 10),
            'phone'               => '0999000002',
            'is_active'           => true,
            'institute_branch_id' => 1,
            'user_id'             => null,
            'photo_path'          => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Data Entry
        |--------------------------------------------------------------------------
        */
        Employee::create([
            'first_name'          => 'ريم',
            'last_name'           => 'صالح',
            'job_title'           => 'مدخلة بيانات',
            'job_type'            => 'data_entry',
            'hire_date'           => Carbon::create(2022, 3, 15),
            'phone'               => '0999000003',
            'is_active'           => true,
            'institute_branch_id' => 1,
            'user_id'             => null,
            'photo_path'          => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Accountant
        |--------------------------------------------------------------------------
        */
        Employee::create([
            'first_name'          => 'مازن',
            'last_name'           => 'درويش',
            'job_title'           => 'محاسب',
            'job_type'            => 'accountant',
            'hire_date'           => Carbon::create(2021, 9, 1),
            'phone'               => '0999000004',
            'is_active'           => true,
            'institute_branch_id' => 2,
            'user_id'             => null,
            'photo_path'          => null,
        ]);
    }
}
