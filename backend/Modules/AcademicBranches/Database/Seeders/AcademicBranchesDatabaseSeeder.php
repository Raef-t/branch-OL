<?php

namespace Modules\AcademicBranches\Database\Seeders;

use Illuminate\Database\Seeder;

class AcademicBranchesDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AcademicBranchesTableSeeder::class,
        ]);
    }
}
