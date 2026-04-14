<?php

namespace Modules\Enrollments\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Modules\Enrollments\Services\StudentEnrollmentService;

class DemoStudentsSeeder extends Seeder
{
    public function run()
    {
        /** @var StudentEnrollmentService $service */
        $service = App::make(StudentEnrollmentService::class);

        for ($i = 1; $i <= 10; $i++) {

            $father = [
                'first_name'  => "أب$i",
                'last_name'   => "العائلة$i",
                'national_id' => "FATH$i",
                'occupation'  => 'موظف',
                'address'     => 'دمشق',
            ];

            $mother = [
                'first_name'  => "أم$i",
                'last_name'   => "العائلة$i",
                'national_id' => "MOTH$i",
                'occupation'  => 'ربة منزل',
                'address'     => 'دمشق',
            ];

            // طالبين لكل عائلة
            for ($s = 1; $s <= 2; $s++) {

                $studentData = [
                    'student' => [
                        'first_name' => "طالب{$i}_{$s}",
                        'last_name'  => "العائلة$i",
                         'date_of_birth' => '2015-01-01',
                          'enrollment_date' => now()->toDateString(), 
                        'gender'     => $s % 2 === 0 ? 'female' : 'male',
                        'city_id'    => 1,
                        'status_id'  => 1,
                        'branch_id'  => 1,
                        'institute_branch_id' => 1,
                        'bus_id'     => null,
                    ],
                    'father' => $father,
                    'mother' => $mother,
                ];

                $service->enrollStudent($studentData, null);
            }
        }

        $this->command->info('✅ تم إنشاء 20 طالب (10 عائلات) ضمن موديول Enrollments');
    }
}
