<?php
$host = '127.0.0.1';
$db   = 'olamaa_institute';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

echo "Connecting to local MySQL at $host...\n";
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Connection successful!\n";
     $stmt = $pdo->query("SHOW DATABASES LIKE '$db'");
     $exists = $stmt->fetch();
     if ($exists) {
         echo "Database '$db' exists.\n";
     } else {
         echo "Database '$db' DOES NOT exist. Creating it...\n";
         $pdo->exec("CREATE DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
         echo "Database '$db' created successfully.\n";
     }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage() . "\n";
}
