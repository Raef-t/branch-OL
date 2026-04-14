<?php

namespace Modules\ClassSchedules\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\InstituteBranches\Models\InstituteBranch;
use Modules\AcademicBranches\Models\AcademicBranch;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\Instructors\Models\Instructor;
use Modules\Subjects\Models\Subject;
use Modules\Batches\Models\Batch;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\InstructorSubjects\Models\InstructorSubject;

class AdvancedSchedulerTestDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Get or Create Institute Branch
        $branch = InstituteBranch::firstOrCreate(
            ['code' => 'TEST01'],
            [
                'name' => 'فرع الاختبار الرئيسي',
                'address' => 'دمشق - المزة',
                'phone' => '0912345678',
            ]
        );

        // 2. Get or Create Academic Branch
        $academicBranch = AcademicBranch::firstOrCreate([
            'name' => 'قسم تكنولوجيا المعلومات',
        ]);

        // 3. Create a New Class Room
        $room = ClassRoom::updateOrCreate(
            ['name' => 'قاعة البيانات 102'],
            [
                'institute_branch_id' => $branch->id,
                'capacity' => 30,
            ]
        );

        // 4. Create New Instructors
        $instructorsData = [
            ['name' => 'د. مروان العلي', 'spec' => 'الذكاء الاصطناعي', 'phone' => '0933001122'],
            ['name' => 'م. سارة المحمود', 'spec' => 'لغة بايثون', 'phone' => '0933001133'],
            ['name' => 'د. سمير الخطيب', 'spec' => 'تعلم الآلة', 'phone' => '0933001144'],
            ['name' => 'م. رنا الجاسم', 'spec' => 'رياضيات البيانات', 'phone' => '0933001155'],
            ['name' => 'د. خالد الشامي', 'spec' => 'هندسة البيانات', 'phone' => '0933001166'],
            ['name' => 'م. نور الصالح', 'spec' => 'علوم البيانات', 'phone' => '0933001177'],
            ['name' => 'د. يوسف الصالح', 'spec' => 'التعلم العميق', 'phone' => '0933001188'],
            ['name' => 'م. أمل العلي', 'spec' => 'البيانات الضخمة', 'phone' => '0933001199'],
        ];

        $instructors = [];
        foreach ($instructorsData as $data) {
            $instructors[] = Instructor::updateOrCreate(
                ['name' => $data['name']],
                [
                    'institute_branch_id' => $branch->id,
                    'phone' => $data['phone'],
                    'specialization' => $data['spec'],
                    'hire_date' => now(),
                ]
            );
        }

        // 5. Create New AI Subjects
        $subjectsData = [
            'لغة بايثون للذكاء الاصطناعي',
            'تعلم الآلة (Machine Learning)',
            'مبادئ هندسة البيانات',
            'الرياضيات المتقدمة للذكاء الاصطناعي',
            'الشبكات العصبية والتعلم العميق'
        ];

        $subjects = [];
        foreach ($subjectsData as $name) {
            $subjects[] = Subject::firstOrCreate(
                ['name' => $name],
                ['academic_branch_id' => $academicBranch->id]
            );
        }

        // 6. Link Instructors to Subjects (Simplified logic for test)
        // Each subject gets 1 or 2 instructors
        $instructorSubjectLinks = [
            0 => [0, 1], // Python AI -> Marwan, Sarah
            1 => [0, 2], // ML -> Marwan, Samir
            2 => [4, 5], // Data Eng -> Khaled, Noor
            3 => [3, 4], // Math AI -> Rana, Khaled
            4 => [6, 7], // Deep Learning -> Youssef, Amal
        ];

        $isLinks = [];
        foreach ($instructorSubjectLinks as $subIdx => $insIdxs) {
            foreach ($insIdxs as $insIdx) {
                $isLinks[$subIdx][] = InstructorSubject::updateOrCreate(
                    [
                        'instructor_id' => $instructors[$insIdx]->id,
                        'subject_id' => $subjects[$subIdx]->id
                    ],
                    ['is_active' => true]
                );
            }
        }

        // 7. Create New Batch
        $batch = Batch::updateOrCreate(
            ['name' => 'دبلوم الذكاء الاصطناعي - الدفعة الثانية'],
            [
                'institute_branch_id' => $branch->id,
                'academic_branch_id' => $academicBranch->id,
                'class_room_id' => $room->id,
                'start_date' => now(),
                'end_date' => now()->addMonths(8),
                'is_hidden' => false,
                'is_archived' => false,
                'gender_type' => 'mixed',
            ]
        );

        // 8. Assign Subjects to Batch with Weekly Lessons
        // Weekly load total: 5+4+3+3+4 = 19 lessons
        $batchSubjectConfigs = [
            0 => ['weekly' => 5, 'ins_idx' => 0], // Python
            1 => ['weekly' => 4, 'ins_idx' => 1], // ML (using instructor 1 from its links)
            2 => ['weekly' => 3, 'ins_idx' => 0], // Data Eng
            3 => ['weekly' => 3, 'ins_idx' => 0], // Math AI
            4 => ['weekly' => 4, 'ins_idx' => 0], // Deep Learning
        ];

        foreach ($batchSubjectConfigs as $subIdx => $config) {
            $isLink = $isLinks[$subIdx][$config['ins_idx']];
            BatchSubject::updateOrCreate(
                ['batch_id' => $batch->id, 'subject_id' => $subjects[$subIdx]->id],
                [
                    'instructor_subject_id' => $isLink->id,
                    'weekly_lessons' => $config['weekly'],
                    'is_active' => true,
                ]
            );
        }

        echo "\n🚀 Advanced Test Data Created Successfully!";
        echo "\nBatch Name: " . $batch->name;
        echo "\nInstructors Created: " . count($instructors);
        echo "\nSubjects Created: " . count($subjects);
        echo "\n------------------------------------------\n";
    }
}
