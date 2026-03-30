<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/mongo_config.php';
use MongoDB\BSON\UTCDateTime;

// Check if admin user already exists
$admin = $usersCollection->findOne(['username' => 'admin']);

if (!$admin) {
    $result = $usersCollection->insertOne([
        'username' => 'admin',
        'email' => 'admin@inventory.com',
        'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin',
        'status' => 'active',
        'created_at' => new \MongoDB\BSON\UTCDateTime()
    ]);

    if ($result->getInsertedCount()) {
        echo "Admin user 'admin' created successfully with password 'admin123'.";
    } else {
        echo "Error: Could not create admin user.";
    }
} else {
    echo "Admin user 'admin' already exists.";
}
?>