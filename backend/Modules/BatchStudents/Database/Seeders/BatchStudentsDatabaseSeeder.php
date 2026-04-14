<?php

namespace Modules\BatchStudents\Database\Seeders;

use Illuminate\Database\Seeder;

class BatchStudentsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            BatchStudentLinksSeeder::class,
        ]);
    }
}
