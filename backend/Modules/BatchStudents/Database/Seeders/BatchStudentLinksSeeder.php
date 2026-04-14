<?php

namespace Modules\BatchStudents\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\BatchStudents\Models\BatchStudent;

class BatchStudentLinksSeeder extends Seeder
{
    public function run()
    {
        // 👇 طلاب من 1 إلى 21
        $studentIds = range(1, 21);

        // 👇 أرقام الدورات التي تريد ربط الطلاب بها
        $batchIds = [
            73, 74, 75, 76, 77, 78, 79, 80, 81, 82,
            83, 84, 85, 86, 87, 88, 89, 90, 93, 94,
        ];

        foreach ($batchIds as $batchId) {
            foreach ($studentIds as $studentId) {

                // ✅ لا تكرر السجل إن كان موجوداً
                BatchStudent::firstOrCreate(
                    [
                        'batch_id'   => $batchId,
                        'student_id' => $studentId,
                    ],
                    [
                        // لو عندك أعمدة إضافية (status مثلاً) أضفها هنا
                    ]
                );
            }
        }
    }
}
