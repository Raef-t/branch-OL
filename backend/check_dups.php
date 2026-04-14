<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$contacts = Modules\ContactDetails\Models\ContactDetail::where('owner_type', 'father')->get();
foreach($contacts as $c) {
    echo "ID: {$c->id}, Value: {$c->value}, FamilyID: {$c->family_id}, GuardianID: {$c->guardian_id}, Primary: {$c->is_primary}\n";
}
