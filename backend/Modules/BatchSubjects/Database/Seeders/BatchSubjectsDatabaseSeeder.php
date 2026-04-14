<?php

namespace Modules\BatchSubjects\Database\Seeders;

use Illuminate\Database\Seeder;

class BatchSubjectsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            BatchSubjectsTableSeeder::class,
        ]);
    }
}
