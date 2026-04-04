<?php
/**
 * Database Configuration Template
 * Copy this file to 'db.php' and fill in your details.
 */

$host = "127.0.0.1";
$port = "3306";
$dbname = "inventory_system";
$username = "root";
$password = ""; // Use your MySQL password here

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    // Error mode and other settings
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
