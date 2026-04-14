<?php

namespace Modules\Subjects\Database\Seeders;

use Illuminate\Database\Seeder;

class SubjectsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SubjectsTableSeeder::class,
        ]);
    }
}
