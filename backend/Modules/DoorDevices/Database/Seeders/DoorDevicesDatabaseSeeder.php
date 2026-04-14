<?php

namespace Modules\DoorDevices\Database\Seeders;

use Illuminate\Database\Seeder;

class DoorDevicesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Modules\DoorDevices\Models\DoorDevice::firstOrCreate(
            ['device_id' => 'DOOR_MAIN_01'],
            [
                'name'      => 'جهاز الباب الرئيسي',
                'location'  => 'المدخل الشمالي',
                'api_key'   => '123',
                'is_active' => true,
            ]
        );
    }
}
