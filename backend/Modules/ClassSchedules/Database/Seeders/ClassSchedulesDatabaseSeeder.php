<?php

namespace Modules\ClassSchedules\Database\Seeders;

use Illuminate\Database\Seeder;

class ClassSchedulesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ClassSchedulesTableSeeder::class,
        ]);
    }
}
