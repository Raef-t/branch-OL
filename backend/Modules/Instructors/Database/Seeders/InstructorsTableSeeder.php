<?php

namespace Modules\Instructors\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Instructors\Models\Instructor;
use Carbon\Carbon;

class InstructorsTableSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = [
            // رياضيات
            ['name' => 'أ. محمد العلي', 'specialization' => 'رياضيات'],
            ['name' => 'أ. أحمد صالح', 'specialization' => 'رياضيات'],

            // فيزياء
            ['name' => 'أ. خالد محمود', 'specialization' => 'فيزياء'],
            ['name' => 'أ. سامر حسن', 'specialization' => 'فيزياء'],

            // كيمياء
            ['name' => 'أ. عمر يوسف', 'specialization' => 'كيمياء'],
            ['name' => 'أ. فراس عبد الله', 'specialization' => 'كيمياء'],

            // علوم
            ['name' => 'أ. لؤي منصور', 'specialization' => 'علوم'],
            ['name' => 'أ. ياسر حمود', 'specialization' => 'علوم'],

            // عربي
            ['name' => 'أ. عبد الرحمن طه', 'specialization' => 'لغة عربية'],
            ['name' => 'أ. محمود الشامي', 'specialization' => 'لغة عربية'],

            // إنكليزي
            ['name' => 'أ. رامي حيدر', 'specialization' => 'لغة إنجليزية'],
            ['name' => 'أ. باسل ناصر', 'specialization' => 'لغة إنجليزية'],

            // تاريخ
            ['name' => 'أ. مازن قاسم', 'specialization' => 'تاريخ'],

            // جغرافيا
            ['name' => 'أ. نزار عبود', 'specialization' => 'جغرافيا'],

            // فلسفة
            ['name' => 'أ. طلال عثمان', 'specialization' => 'فلسفة'],

            // ديانة
            ['name' => 'أ. عبد الكريم صالح', 'specialization' => 'تربية دينية'],

            // وطنية
            ['name' => 'أ. جمال حسين', 'specialization' => 'تربية وطنية'],
        ];

        foreach ($instructors as $index => $data) {
            Instructor::firstOrCreate(
                [
                    'name' => $data['name'],
                ],
                [
                    'phone'              => '09' . rand(10000000, 99999999),
                    'specialization'     => $data['specialization'],
                    'hire_date'          => Carbon::now()->subYears(rand(1, 10))->toDateString(),
                    'profile_photo_url'  => null,
                ]
            );
        }
    }
}
