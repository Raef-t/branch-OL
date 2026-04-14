<?php

namespace Modules\InstructorSubjects\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\InstructorSubjects\Models\InstructorSubject;
use Modules\Instructors\Models\Instructor;
use Modules\Subjects\Models\Subject;

class InstructorSubjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::all();
        $instructors = Instructor::all()->groupBy('specialization');

        if ($subjects->isEmpty() || $instructors->isEmpty()) {
            throw new \Exception('يجب تشغيل Seeder المواد والأساتذة أولاً');
        }

        foreach ($subjects as $subject) {
            // محاولة إيجاد أستاذ بنفس الاختصاص
            $possibleInstructors = $instructors[$subject->name]
                ?? $instructors->filter(function ($items, $key) use ($subject) {
                    return str_contains($key, $subject->name)
                        || str_contains($subject->name, $key);
                })->flatten();

            // في حال عدم وجود تطابق مباشر
            if ($possibleInstructors->isEmpty()) {
                $possibleInstructors = Instructor::inRandomOrder()->limit(1)->get();
            }

            // نربط المادة بأستاذ واحد (أو أكثر لو أحببت لاحقًا)
            foreach ($possibleInstructors->take(1) as $instructor) {
                InstructorSubject::firstOrCreate(
                    [
                        'instructor_id' => $instructor->id,
                        'subject_id'    => $subject->id,
                    ],
                    [
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
