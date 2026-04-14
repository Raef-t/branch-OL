<?php

namespace Modules\Cities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Cities\Models\City;

class CitiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'دمشق',
            'ريف دمشق',
            'حلب',
            'حمص',
            'حماة',
            'اللاذقية',
            'طرطوس',
            'إدلب',
            'دير الزور',
            'الرقة',
            'الحسكة',
            'درعا',
            'السويداء',
            'القنيطرة',
            
        ];

        foreach ($cities as $cityName) {
            City::firstOrCreate(
                ['name' => $cityName],
                [
                    'description' => 'مدينة سورية',
                    'is_active'   => true,
                ]
            );
        }
    }
}
