<?php

namespace Modules\BatchSubjects\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\Batches\Models\Batch;
use Modules\Subjects\Models\Subject;
use Modules\InstructorSubjects\Models\InstructorSubject;
use Modules\ClassRooms\Models\ClassRoom;

class BatchSubjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        $batches = Batch::all();
        $classRooms = ClassRoom::all();

        if ($batches->isEmpty()) {
            throw new \Exception('لا توجد دفعات، شغّل Seeder Batches أولاً');
        }

        if ($classRooms->isEmpty()) {
            throw new \Exception('لا توجد قاعات، شغّل Seeder ClassRooms أولاً');
        }

        $roomIndex = 0;

        foreach ($batches as $batch) {

            // المواد الخاصة بالفرع الأكاديمي للدفعة
            $subjects = Subject::where(
                'academic_branch_id',
                $batch->academic_branch_id
            )->get();

            foreach ($subjects as $subject) {

                // إيجاد الربط المناسب (أستاذ + مادة)
                $instructorSubject = InstructorSubject::where('subject_id', $subject->id)
                    ->where('is_active', true)
                    ->first();

                if (!$instructorSubject) {
                    // لا يوجد أستاذ لهذه المادة
                    continue;
                }

                // اختيار قاعة بالتدوير
                $classRoom = $classRooms[$roomIndex % $classRooms->count()];
                $roomIndex++;

                BatchSubject::firstOrCreate(
                    [
                        'batch_id'   => $batch->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'instructor_subject_id' => $instructorSubject->id,
                        'class_room_id'         => $classRoom->id,

                        // ⬅ الحقول أصبحت اختيارية
                        'assignment_date'       => null,
                     

                        'is_active'             => true,
                        'notes'                 => null,
                    ]
                );
            }
        }
    }
}
