<?php

namespace Modules\ClassRooms\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ClassRooms\Models\ClassRoom;

class ClassRoomsTableSeeder extends Seeder
{
    public function run(): void
    {
        // القاعات من 1 إلى 9
        for ($i = 1; $i <= 9; $i++) {
            ClassRoom::firstOrCreate(
                ['code' => 'CR-' . $i],
                [
                    'name'     => 'القاعة ' . $i,
                    'capacity' => 30,
                    'notes'    => null,
                ]
            );
        }
    }
}
