<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Setting;

class AAASettingsFixSeeder extends Seeder
{
    public function run()
    {
        Setting::firstOrCreate([], [
            'is_system_enabled' => true,
            'maintenance_message' => null,
        ]);
    }
}

