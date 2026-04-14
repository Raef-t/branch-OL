<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$families = Modules\Families\Models\Family::with('contactDetails')->get();
$familiesWithContacts = 0;
foreach($families as $f) {
  if($f->contactDetails->count() > 0) {
     $familiesWithContacts++;
     echo "Family ID {$f->id} has {$f->contactDetails->count()} contacts.\n";
  }
}
echo "Families with contacts = {$familiesWithContacts}";
