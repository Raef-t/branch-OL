<?php

namespace Modules\ClassRooms\Database\Seeders;

use Illuminate\Database\Seeder;

class ClassRoomsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $this->call([
            ClassRoomsTableSeeder::class,
        ]);
    }

}
