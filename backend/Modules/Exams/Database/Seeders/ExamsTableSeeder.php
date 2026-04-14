<?php


namespace Modules\Exams\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Exams\Models\Exam;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\ExamTypes\Models\ExamType;
use Carbon\Carbon;

class ExamsTableSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 جلب أنواع الامتحانات
        $examTypes = ExamType::all();

        if ($examTypes->count() < 1) {
            $this->command->warn('لا يوجد Exam Types');
            return;
        }

        // 🔹 جلب Batch Subjects المطلوبة
        $batchSubjects = BatchSubject::with('subject')
            ->whereBetween('id', [725, 750])
            ->get();

        foreach ($batchSubjects as $batchSubject) {

            $subjectName = strtolower($batchSubject->subject->name ?? '');

            // 🔹 تحديد العلامات حسب المادة
            [$totalMarks, $passingMarks] = $this->resolveMarks($subjectName);

            foreach ($examTypes as $index => $examType) {

                Exam::create([
                    'batch_subject_id' => $batchSubject->id,
                    'name'             => $examType->name . ' - ' . $batchSubject->subject->name,
                    'exam_date'        => Carbon::now()->addDays(rand(5, 30)),
                    'exam_time'        => $index === 0 ? '09:00' : '12:00',
                    'total_marks'      => $totalMarks,
                    'passing_marks'    => $passingMarks,
                    'status'           => 'scheduled',
                    'exam_type_id'     => $examType->id,
                    'remarks'          => 'تم إنشاؤه تلقائياً عبر Seeder',
                ]);
            }
        }

        $this->command->info('✔ Exams Seeder executed successfully');
    }

    /**
     * 🎯 تحديد العلامات حسب اسم المادة
     */
    private function resolveMarks(string $subjectName): array
    {
        if (str_contains($subjectName, 'رياض')) {
            return [600, 300];
        }

        if (str_contains($subjectName, 'فيزي') || str_contains($subjectName, 'كيمي')) {
            return [600, 300];
        }

        if (str_contains($subjectName, 'عربي') || str_contains($subjectName, 'انج') || str_contains($subjectName, 'english')) {
            return [400, 200];
        }

        if (str_contains($subjectName, 'ديان')) {
            return [200, 100];
        }

        // 🔸 افتراضي
        return [300, 150];
    }
}
