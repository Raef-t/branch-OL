<?php

namespace Modules\Batches\Database\Seeders;

use Illuminate\Database\Seeder;

class BatchesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $this->call([
            BatchesTableSeeder::class,
        ]);
    }
}
