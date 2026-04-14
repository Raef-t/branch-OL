<?php

namespace Modules\KnowledgeSources\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\KnowledgeSources\Models\KnowledgeSource;

class KnowledgeSourcesTableSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'name' => 'الدليل',
                'description' => 'عن طريق دليل المعهد',
            ],
            [
                'name' => 'صديق',
                'description' => 'عن طريق توصية صديق',
            ],
            [
                'name' => 'الإنترنت',
                'description' => 'عن طريق البحث أو الإعلانات على الإنترنت',
            ],
            [
                'name' => 'مدرس',
                'description' => 'عن طريق مدرس',
            ],
            [
                'name' => 'مدرسة مجاورة',
                'description' => 'عن طريق مدرسة قريبة من المعهد',
            ],
            [
                'name' => 'إعلان شارع',
                'description' => 'عن طريق إعلان في الشارع',
            ],
            [
                'name' => 'برشور',
                'description' => 'عن طريق منشور أو برشور',
            ],
            [
                'name' => 'جوار المعهد',
                'description' => 'عن طريق السكن أو التواجد قرب المعهد',
            ],
        ];

        foreach ($sources as $source) {
            KnowledgeSource::firstOrCreate(
                ['name' => $source['name']],
                [
                    'description' => $source['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
