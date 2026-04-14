<?php

namespace Modules\ExamResults\Database\Seeders;

use Illuminate\Database\Seeder;

class ExamResultsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
{
    $this->call(ExamResultsSeeder::class);
}
}
