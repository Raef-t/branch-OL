<?php

namespace Modules\ExamResults\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ExamResults\Models\ExamResult;

class ExamResultsSeeder extends Seeder
{
    public function run()
    {
        $examIds    = range(1, 52);
        $studentIds = range(1, 21);

        foreach ($examIds as $examId) {

            // عدد طلاب عشوائي لكل امتحان
            $studentsForExam = collect($studentIds)
                ->shuffle()
                ->take(rand(10, 21));

            foreach ($studentsForExam as $studentId) {

                $mark = rand(20, 100);
                $isPassed = $mark >= 50;

                // تحديد الملاحظة
                if ($mark >= 90) {
                    $remark = 'ممتاز 🌟';
                } elseif ($mark >= 80) {
                    $remark = 'جيد جدًا 🟢';
                } elseif ($mark >= 70) {
                    $remark = 'جيد 🟡';
                } elseif ($mark >= 50) {
                    $remark = 'مقبول 🟠';
                } else {
                    $remark = 'راسب 🔴';
                }

                ExamResult::create([
                    'exam_id'        => $examId,
                    'student_id'     => $studentId,
                    'obtained_marks' => $mark,
                    'is_passed'      => $isPassed,
                    'remarks'        => $remark,
                ]);
            }
        }
    }
}
