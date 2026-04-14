<?php

namespace Modules\ExamTypes\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ExamTypes\Models\ExamType;

class ExamTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'test',
                'description' => 'اختبار قصير أو دوري',
            ],
            [
                'name' => 'exam',
                'description' => 'امتحان رسمي أو نهائي',
            ],
        ];

        foreach ($types as $type) {
            ExamType::firstOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']]
            );
        }
    }
}
