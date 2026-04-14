<?php

namespace Modules\Subjects\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Subjects\Models\Subject;
use Modules\AcademicBranches\Models\AcademicBranch;

class SubjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        // 🧹 حذف المواد الحالية فقط (بدون كسر FK)
        DB::table('subjects')->delete();

        // 🔎 جلب الفروع الأساسية (حسب الأسماء الموجودة فعليًا)
        $ninth = AcademicBranch::where('name', 'تاسع')->first();
        $scientific = AcademicBranch::where('name', 'بكالوريا علمي')->first();
        $literary = AcademicBranch::where('name', 'بكالوريا أدبي')->first();

        if (!$ninth || !$scientific || !$literary) {
            throw new \Exception('أحد الفروع الأكاديمية الأساسية غير موجود (تاسع / بكالوريا علمي / بكالوريا أدبي)');
        }

        /*
        |--------------------------------------------------------------------------
        | مواد الصف التاسع (المنهاج السوري)
        |--------------------------------------------------------------------------
        */
        $ninthSubjects = [
            'اللغة العربية',
            'اللغة الإنجليزية',
            'الرياضيات',
            'العلوم العامة',
            'الفيزياء',
            'الكيمياء',
            'التاريخ',
            'الجغرافيا',
            'التربية الدينية',
            'التربية الوطنية',
        ];

        foreach ($ninthSubjects as $name) {
            Subject::create([
                'name' => $name,
                'academic_branch_id' => $ninth->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | مواد البكالوريا العلمي
        |--------------------------------------------------------------------------
        */
        $scientificSubjects = [
            'اللغة العربية',
            'اللغة الإنجليزية',
            'الرياضيات',
            'الفيزياء',
            'الكيمياء',
            'العلوم',
            'التربية الدينية',
            'التربية الوطنية',
        ];

        foreach ($scientificSubjects as $name) {
            Subject::create([
                'name' => $name,
                'academic_branch_id' => $scientific->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | مواد البكالوريا الأدبي
        |--------------------------------------------------------------------------
        */
        $literarySubjects = [
            'اللغة العربية',
            'اللغة الإنجليزية',
            'التاريخ',
            'الجغرافيا',
            'الفلسفة',
            'التربية الدينية',
            'التربية الوطنية',
        ];

        foreach ($literarySubjects as $name) {
            Subject::create([
                'name' => $name,
                'academic_branch_id' => $literary->id,
            ]);
        }
    }
}
