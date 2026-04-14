<?php
$host = '127.0.0.1';
$db   = 'olamaa_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     echo "--- Latest 5 Attendance Records ---\n";
     $stmt = $pdo->query("SELECT * FROM attendances ORDER BY id DESC LIMIT 5");
     while ($row = $stmt->fetch()) {
         print_r($row);
     }

     echo "\n--- Ahmed Al-Khatib Check ---\n";
     // Since names are hashed, we look for recent students or those in batches
     $stmt = $pdo->query("SELECT id, first_name_hash, institute_branch_id FROM students ORDER BY id DESC LIMIT 20");
     while ($row = $stmt->fetch()) {
         // We can't see the name easily without the model, but we can see IDs
         print_r($row);
     }
     
     echo "\n--- Batch Status ---\n";
     $stmt = $pdo->query("SELECT id, name, is_hidden, is_archived FROM batches LIMIT 50");
     while ($row = $stmt->fetch()) {
         print_r($row);
     }

} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
