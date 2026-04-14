<?php
$host = 'mysql8002.site4now.net';
$db   = 'db_ac4e98_norma91';
$user = 'ac4e98_norma91';
$pass = 'Passraef900';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

echo "Testing connection to $host...\n";
try {
     $pdo = new PDO($dsn, $user, $pass);
     echo "Connection successful!\n";
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage() . "\n";
}
