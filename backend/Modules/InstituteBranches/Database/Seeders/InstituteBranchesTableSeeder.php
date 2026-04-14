<?php

namespace Modules\InstituteBranches\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\InstituteBranches\Models\InstituteBranch;

class InstituteBranchesTableSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name'         => 'فرع حلب الجديدة',
                'address'      => 'حلب – حلب الجديدة',
                'code'         => 'ALEPPO-JD',
                'country_code' => '+963',
                'phone'        => '021000000',
                'email'        => 'newaleppo@institute.com',
                'manager_name' => 'مدير الفرع',
                'is_active'    => true,
            ],
            [
                'name'         => 'فرع الفرقان',
                'address'      => 'حلب – الفرقان',
                'code'         => 'ALEPPO-FQ',
                'country_code' => '+963',
                'phone'        => '021111111',
                'email'        => 'furqan@institute.com',
                'manager_name' => 'مدير الفرع',
                'is_active'    => true,
            ],
        ];

        foreach ($branches as $branch) {
            InstituteBranch::firstOrCreate(
                ['code' => $branch['code']], // مفتاح فريد منطقي
                $branch
            );
        }
    }
}
