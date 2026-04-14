<?php

namespace Modules\MessageTemplates\Database\Seeders;

use Illuminate\Database\Seeder;

class MessageTemplatesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $this->call([
            MessageTemplatesTableSeeder::class,
        ]);
    }
}
