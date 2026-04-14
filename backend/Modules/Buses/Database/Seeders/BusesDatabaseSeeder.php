<?php

namespace Modules\Buses\Database\Seeders;

use Illuminate\Database\Seeder;

class BusesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
    {
        $this->call([
            BusesTableSeeder::class,
        ]);
    }
}
