<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$contacts = Modules\ContactDetails\Models\ContactDetail::all();
foreach($contacts as $c) {
   echo "ID: {$c->id}, FamilyID: {$c->family_id}, Type: {$c->type}, Value: {$c->value}, Owner: {$c->owner_type}\n";
}
