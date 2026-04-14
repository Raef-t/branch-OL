<?php

namespace Modules\KnowledgeSources\Database\Seeders;

use Illuminate\Database\Seeder;

class KnowledgeSourcesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $this->call([
            KnowledgeSourcesTableSeeder::class,
        ]);
    }
}
