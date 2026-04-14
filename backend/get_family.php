<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$family = Modules\Families\Models\Family::whereHas('contactDetails')->with(['guardians'])->inRandomOrder()->first();

if($family) {
    $father = $family->guardians->where('relationship', 'father')->first();
    $mother = $family->guardians->where('relationship', 'mother')->first();
    echo "Family Found ID: {$family->id}\n";
    echo "Father First: " . ($father ? $father->first_name : 'No') . "\n";
    echo "Father Last: " . ($father ? $father->last_name : 'No') . "\n";
    echo "Mother First: " . ($mother ? $mother->first_name : 'No') . "\n";
    echo "Mother Last: " . ($mother ? $mother->last_name : 'No') . "\n";
    echo "Contacts: " . $family->contactDetails()->count() . "\n";
} else {
    echo "No family with contacts found!\n";
}
