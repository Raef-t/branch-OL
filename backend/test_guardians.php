<?php
$response = file_get_contents('http://localhost:8000/api/students/details');
$data = json_decode($response, true);
$found_guardians = false;

if (isset($data['data']) && is_array($data['data'])) {
    foreach ($data['data'] as $student) {
        if (isset($student['family']['guardians']) && count($student['family']['guardians']) > 0) {
            echo "Found guardians for student: " . $student['full_name'] . "\n";
            print_r($student['family']['guardians']);
            $found_guardians = true;
            break;
        }
    }
}

if (!$found_guardians) {
    echo "No guardians found in the response.\n";
}
