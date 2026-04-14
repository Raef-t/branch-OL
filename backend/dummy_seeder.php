<?php
use Modules\StudentStatuses\Models\StudentStatus;
use Modules\Families\Models\Family;
use Modules\Guardians\Models\Guardian;
use Modules\ContactDetails\Models\ContactDetail;
use Modules\Students\Models\Student;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\EnrollmentContracts\Models\EnrollmentContract;
use Modules\Payments\Models\Payment;
use Carbon\Carbon;

echo "--- Starting Professional Seeding ---\n";

try {
    // 1. Ensure Status exists
    $status = StudentStatus::firstOrCreate(['name' => 'مسجل']);
    echo "Status 'مسجل' ready.\n";

    // 2. Data Arrays
    $firstNames = ['محمد', 'أحمد', 'عمر', 'علي', 'خالد', 'فاطمة', 'زينب', 'ليلى', 'رشا', 'سامر'];
    $lastNames = ['الخطيب', 'المصري', 'إدريس', 'حلبي', 'سيد', 'عباس', 'نجار', 'قاسم', 'حمد', 'صادق'];
    $fatherNames = ['يحيى', 'باسل', 'محمود', 'عبد الرحمن', 'هشام'];

    // 3. Create 5 Families and their relations
    for ($i = 0; $i < 5; $i++) {
        $family = Family::create(['user_id' => null]);
        echo "Created Family " . ($i + 1) . "\n";
        
        // Father
        $father = Guardian::create([
            'family_id' => $family->id,
            'first_name' => $fatherNames[$i],
            'last_name' => $lastNames[$i],
            'relationship' => 'father',
            'is_primary_contact' => true,
            'national_id' => '020100' . rand(100000, 999999),
            'phone' => '093' . rand(1000000, 9999999)
        ]);
        
        ContactDetail::create([
            'guardian_id' => $father->id,
            'family_id' => $family->id,
            'type' => 'phone',
            'value' => $father->phone,
            'is_primary' => true,
            'supports_whatsapp' => true,
            'supports_call' => true,
            'supports_sms' => true
        ]);

        // Mother
        $mother = Guardian::create([
            'family_id' => $family->id,
            'first_name' => 'أميرة',
            'last_name' => $lastNames[$i],
            'relationship' => 'mother',
            'is_primary_contact' => false,
            'national_id' => '020101' . rand(100000, 999999),
            'phone' => '098' . rand(1000000, 9999999)
        ]);

        // 4. Create 2 Students per family
        for ($j = 0; $j < 2; $j++) {
            $studentIdx = ($i * 2) + $j;
            $student = Student::create([
                'family_id' => $family->id,
                'institute_branch_id' => rand(1, 3),
                'first_name' => $firstNames[$studentIdx],
                'last_name' => $lastNames[$i],
                'date_of_birth' => Carbon::now()->subYears(rand(7, 15)),
                'enrollment_date' => Carbon::now(),
                'gender' => ($studentIdx % 2 == 0) ? 'male' : 'female',
                'status_id' => $status->id,
                'branch_id' => rand(1, 3), 
                'bus_id' => rand(1, 4)
            ]);

            echo "  - Added Student: " . $student->first_name . " " . $student->last_name . "\n";

            // Enrollment in a batch
            BatchStudent::create([
                'batch_id' => rand(1, 2),
                'student_id' => $student->id
            ]);

            // Financial Contract
            $total = rand(5000, 10000);
            $contract = EnrollmentContract::create([
                'student_id' => $student->id,
                'total_amount_usd' => $total,
                'final_amount_usd' => $total,
                'paid_amount_usd' => 0,
                'exchange_rate_at_enrollment' => 15000,
                'final_amount_syp' => $total * 15000,
                'is_active' => true,
                'agreed_at' => Carbon::now()
            ]);

            // Payment
            $p1 = rand(1000, 2500);
            Payment::create([
                'enrollment_contract_id' => $contract->id,
                'institute_branch_id' => $student->institute_branch_id,
                'amount_usd' => $p1,
                'currency' => 'USD',
                'paid_date' => Carbon::now(),
                'receipt_number' => 'REC-' . rand(10000, 99999),
                'description' => 'الدفعة الأولى للتسجيل'
            ]);
            
            $contract->update(['paid_amount_usd' => $p1]);
        }
    }
    echo "--- Seeding Completed Successfully ---\n";
} catch (\Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
