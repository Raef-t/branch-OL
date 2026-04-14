<?php

namespace Modules\Instructors\Database\Seeders;

use Illuminate\Database\Seeder;

class InstructorsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            InstructorsTableSeeder::class,
        ]);
    }
}
