<?php

namespace Modules\InstituteBranches\Database\Seeders;

use Illuminate\Database\Seeder;

class InstituteBranchesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            InstituteBranchesTableSeeder::class,
        ]);
    }
}
