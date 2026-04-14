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

class SchedulerTestDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Institute Branch
        $branch = InstituteBranch::updateOrCreate(
            ['code' => 'TEST01'],
            [
                'name' => 'فرع الاختبار الرئيسي',
                'address' => 'دمشق - المزة',
                'phone' => '0912345678',
            ]
        );

        // 2. Create Academic Branch
        $academicBranch = AcademicBranch::firstOrCreate([
            'name' => 'قسم تكنولوجيا المعلومات',
        ]);

        // 3. Create Class Room
        $room = ClassRoom::updateOrCreate(
            ['name' => 'قاعة البرمجة 101'],
            [
                'institute_branch_id' => $branch->id,
                'capacity' => 25,
            ]
        );

        // 4. Create Instructors
        $instructor1 = Instructor::updateOrCreate(
            ['name' => 'د. أحمد المحمد'],
            [
                'institute_branch_id' => $branch->id,
                'phone' => '0933112233',
                'specialization' => 'علوم حاسب',
                'hire_date' => now(),
            ]
        );

        $instructor2 = Instructor::updateOrCreate(
            ['name' => 'م. ليلى حسن'],
            [
                'institute_branch_id' => $branch->id,
                'phone' => '0944556677',
                'specialization' => 'رياضيات',
                'hire_date' => now(),
            ]
        );

        // 5. Create Subjects
        $subject1 = Subject::firstOrCreate(['name' => 'أساسيات البرمجة'], ['academic_branch_id' => $academicBranch->id]);
        $subject2 = Subject::firstOrCreate(['name' => 'الخوارزميات'], ['academic_branch_id' => $academicBranch->id]);
        $subject3 = Subject::firstOrCreate(['name' => 'قواعد البيانات'], ['academic_branch_id' => $academicBranch->id]);

        // 6. Link Instructors to Subjects
        $is1 = InstructorSubject::updateOrCreate(
            ['instructor_id' => $instructor1->id, 'subject_id' => $subject1->id],
            ['is_active' => true]
        );

        $is2 = InstructorSubject::updateOrCreate(
            ['instructor_id' => $instructor1->id, 'subject_id' => $subject2->id],
            ['is_active' => true]
        );

        $is3 = InstructorSubject::updateOrCreate(
            ['instructor_id' => $instructor2->id, 'subject_id' => $subject3->id],
            ['is_active' => true]
        );

        // 7. Create Batch
        $batch = Batch::updateOrCreate(
            ['name' => 'دورة البرمجة الشاملة - دفعة 2026'],
            [
                'institute_branch_id' => $branch->id,
                'academic_branch_id' => $academicBranch->id,
                'class_room_id' => $room->id,
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
                'is_hidden' => false,
                'is_archived' => false,
            ]
        );

        // 8. Assign Subjects to Batch with Weekly Lessons
        BatchSubject::updateOrCreate(
            ['batch_id' => $batch->id, 'subject_id' => $subject1->id],
            [
                'instructor_subject_id' => $is1->id,
                'weekly_lessons' => 4,
                'is_active' => true,
            ]
        );

        BatchSubject::updateOrCreate(
            ['batch_id' => $batch->id, 'subject_id' => $subject2->id],
            [
                'instructor_subject_id' => $is2->id,
                'weekly_lessons' => 3,
                'is_active' => true,
            ]
        );

        BatchSubject::updateOrCreate(
            ['batch_id' => $batch->id, 'subject_id' => $subject3->id],
            [
                'instructor_subject_id' => $is3->id,
                'weekly_lessons' => 3,
                'is_active' => true,
            ]
        );

        echo "\n✅ Test Data Created Successfully!";
        echo "\nBatch ID: " . $batch->id . "\n";
    }
}
