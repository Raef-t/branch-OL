<?php

namespace Modules\Buses\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Buses\Models\Bus;

class BusesTableSeeder extends Seeder
{
    public function run(): void
    {
        $buses = [
            [
                'name'              => 'أبو عمر',
                'capacity'          => 30,
                'driver_name'       => 'أبو عمر',
                'route_description' => 'خط حلب الجديدة – الفرقان',
                'is_active'         => true,
            ],
            [
                'name'              => 'عمار كرمان',
                'capacity'          => 30,
                'driver_name'       => 'عمار كرمان',
                'route_description' => 'خط الحمدانية – الفرقان',
                'is_active'         => true,
            ],
            [
                'name'              => 'الخال',
                'capacity'          => 25,
                'driver_name'       => 'الخال',
                'route_description' => 'خط صلاح الدين – حلب الجديدة',
                'is_active'         => true,
            ],
            [
                'name'              => 'هشام',
                'capacity'          => 25,
                'driver_name'       => 'هشام',
                'route_description' => 'خط السكري – الفرقان',
                'is_active'         => true,
            ],
        ];

        foreach ($buses as $bus) {
            Bus::firstOrCreate(
                ['name' => $bus['name']], // مفتاح منطقي فريد
                $bus
            );
        }
    }
}
