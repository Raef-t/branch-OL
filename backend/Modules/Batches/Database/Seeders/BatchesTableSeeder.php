<?php

namespace Modules\Batches\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Batches\Models\Batch;
use Modules\InstituteBranches\Models\InstituteBranch;
use Modules\AcademicBranches\Models\AcademicBranch;
use Modules\ClassRooms\Models\ClassRoom;
use Carbon\Carbon;

class BatchesTableSeeder extends Seeder
{
    public function run(): void
    {
        // جلب الفروع
        $instituteBranches = InstituteBranch::all()->keyBy('code');
        $academicBranches  = AcademicBranch::all()->keyBy('name');
        $classRooms        = ClassRoom::all()->values();

        if ($instituteBranches->isEmpty() || $academicBranches->isEmpty() || $classRooms->isEmpty()) {
            throw new \Exception('بيانات أساسية ناقصة (فروع / قاعات)');
        }

        $seasons = [
            ['name' => 'شتاء', 'start' => '01-01', 'end' => '05-31'],
            ['name' => 'صيف',  'start' => '06-01', 'end' => '09-30'],
        ];

        $genders = [
            'male'   => 'شباب',
            'female' => 'بنات',
            'mixed'  => 'مختلط',
        ];

        $years = range(2020, 2024);

        $roomIndex = 0;

        foreach ($years as $year) {
            foreach ($seasons as $season) {
                foreach ($academicBranches as $academicBranch) {
                    foreach ($genders as $genderKey => $genderLabel) {

                        // اختيار قاعة بالتدوير
                        $classRoom = $classRooms[$roomIndex % $classRooms->count()];
                        $roomIndex++;

                        $startDate = Carbon::createFromFormat(
                            'Y-m-d',
                            $year . '-' . $season['start']
                        );

                        $endDate = Carbon::createFromFormat(
                            'Y-m-d',
                            $year . '-' . $season['end']
                        );

                        $batchName = sprintf(
                            '%s %s %s %d',
                            $academicBranch->name,
                            $genderLabel,
                            $season['name'],
                            $year
                        );

                        Batch::firstOrCreate(
                            [
                                'name' => $batchName,
                            ],
                            [
                                'institute_branch_id' => $instituteBranches->first()->id,
                                'academic_branch_id'  => $academicBranch->id,
                                'class_room_id'       => $classRoom->id,
                                'start_date'          => $startDate,
                                'end_date'            => $endDate,
                                'gender_type'         => $genderKey,
                                'is_archived'         => false,
                                'is_hidden'           => false,
                                'is_completed'        => false,
                            ]
                        );
                    }
                }
            }
        }
    }
}
