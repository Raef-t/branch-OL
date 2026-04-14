<?php

namespace Modules\InstructorSubjects\Database\Seeders;

use Illuminate\Database\Seeder;

class InstructorSubjectsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            InstructorSubjectsTableSeeder::class,
        ]);
    }
}
