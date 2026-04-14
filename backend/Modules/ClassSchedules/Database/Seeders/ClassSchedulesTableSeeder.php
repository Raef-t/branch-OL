<?php

namespace Modules\ClassSchedules\Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Modules\ClassSchedules\Models\ClassSchedule;
use Modules\BatchSubjects\Models\BatchSubject;

class ClassSchedulesTableSeeder extends Seeder
{
    public function run(): void
    {
        // 🎯 الباتشات المستهدفة
        $targetBatchIds = [73, 76, 79];

        // 🗓 أيام الدوام
        $daysOfWeek = [
            'saturday',
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
        ];

        // ⏱ 5 حصص – تبدأ 08:00 – ساعة لكل حصة
        $periods = [];
        $start = Carbon::createFromTime(8, 0);

        for ($i = 1; $i <= 5; $i++) {
            $end = (clone $start)->addHour();
            $periods[$i] = [
                $start->format('H:i:s'),
                $end->format('H:i:s'),
            ];
            $start = $end;
        }

        // 🔹 جلب المواد مجمّعة حسب batch
        $batchSubjectsByBatch = BatchSubject::whereIn('batch_id', $targetBatchIds)
            ->with('classRoom')
            ->get()
            ->groupBy('batch_id');

        if ($batchSubjectsByBatch->isEmpty()) {
            $this->command->warn('⚠️ لا توجد مواد للباتشات المحددة');
            return;
        }

        // 🧹 حذف الجداول القديمة لهذه الباتشات
        ClassSchedule::whereIn(
            'batch_subject_id',
            $batchSubjectsByBatch->flatten()->pluck('id')
        )->delete();

        foreach ($batchSubjectsByBatch as $batchId => $batchSubjects) {

            foreach ($daysOfWeek as $day) {

                // 🔀 خلط المواد وأخذ 5 فقط
                $dailySubjects = $batchSubjects->shuffle()->take(5)->values();

                foreach ($dailySubjects as $index => $batchSubject) {

                    $periodNumber = $index + 1;
                    [$startTime, $endTime] = $periods[$periodNumber];

                    // ✅ البرنامج الافتراضي
                    ClassSchedule::create([
                        'batch_subject_id' => $batchSubject->id,
                        'day_of_week'      => $day,
                        'period_number'    => $periodNumber,
                        'start_time'       => $startTime,
                        'end_time'         => $endTime,
                        'class_room_id'    => $batchSubject->class_room_id,
                        'is_default'       => true,
                        'is_active'        => true,
                        'description'      => 'البرنامج الافتراضي',
                    ]);

                    // 🌙 برنامج رمضان (غير افتراضي)
                    ClassSchedule::create([
                        'batch_subject_id' => $batchSubject->id,
                        'day_of_week'      => $day,
                        'period_number'    => $periodNumber,
                        'start_time'       => $startTime,
                        'end_time'         => $endTime,
                        'class_room_id'    => $batchSubject->class_room_id,
                        'is_default'       => false,
                        'is_active'        => true,
                        'description'      => 'برنامج رمضان',
                    ]);
                }
            }
        }

        $this->command->info('✅ تم إنشاء جداول بدون تكرار مواد في نفس اليوم');
    }
}
