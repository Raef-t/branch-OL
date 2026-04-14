<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "Total Contacts: " . Modules\ContactDetails\Models\ContactDetail::count() . "\n";
