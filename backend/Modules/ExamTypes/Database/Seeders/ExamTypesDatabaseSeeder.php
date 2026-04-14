<?php

namespace Modules\ExamTypes\Database\Seeders;

use Illuminate\Database\Seeder;

class ExamTypesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
     {
        $this->call([
            ExamTypesTableSeeder::class,
        ]);
    }
}
