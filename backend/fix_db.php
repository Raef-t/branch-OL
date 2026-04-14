<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Fix orphaned contacts by guardian
$contactsByGuardian = Modules\ContactDetails\Models\ContactDetail::whereNull('family_id')->whereNotNull('guardian_id')->get();
foreach($contactsByGuardian as $c) {
    if ($c->guardian) {
        $c->family_id = $c->guardian->family_id;
        $c->save();
        echo "Fixed contact {$c->id} using guardian's family_id {$c->family_id}\n";
    }
}

// Fix orphaned contacts by student
$contactsByStudent = Modules\ContactDetails\Models\ContactDetail::whereNull('family_id')->whereNotNull('student_id')->get();
foreach($contactsByStudent as $c) {
    if ($c->student) {
        $c->family_id = $c->student->family_id;
        $c->save();
        echo "Fixed contact {$c->id} using student's family_id {$c->family_id}\n";
    }
}
echo "Done fixing DB.\n";
