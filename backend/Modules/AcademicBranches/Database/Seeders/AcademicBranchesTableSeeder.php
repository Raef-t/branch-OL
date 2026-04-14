<?php

namespace Modules\AcademicBranches\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\AcademicBranches\Models\AcademicBranch;

class AcademicBranchesTableSeeder extends Seeder
{
    public function run(): void
    {
        // 🧹 حذف جميع السجلات الحالية
        // DB::table('academic_branches')->truncate();

        // 📚 الفروع الأكاديمية الأساسية
        $branches = [
            [
                'name' => 'بكالوريا علمي',
                'description' => 'الفرع العلمي لطلاب المرحلة الثانوية',
            ],
            [
                'name' => 'بكالوريا أدبي',
                'description' => 'الفرع الأدبي لطلاب المرحلة الثانوية',
            ],
            [
                'name' => 'تاسع',
                'description' => 'طلاب الصف التاسع الأساسي',
            ],
        ];

        foreach ($branches as $branch) {
            AcademicBranch::create($branch);
        }
    }
}
