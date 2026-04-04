<?php
/**
 * MongoDB Configuration Template
 * Copy this file to 'mongo_config.php' and fill in your details.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;

try {
    // path to your cacert.pem file
    $caPath = realpath(__DIR__ . '/../cacert.pem');
    
    // Replace with your actual MongoDB Atlas Connection String
    $uri = "mongodb+srv://<USERNAME>:<PASSWORD>@<CLUSTER_URL>/inventory_system?retryWrites=true&w=majority&appName=Cluster0&tlsCAFile=" . urlencode($caPath);
    
    // Select database and collections
    $client = new Client($uri, [], ["serverSelectionTimeoutMS" => 5000]);
    $database = $client->selectDatabase('inventory_system');
    $usersCollection = $database->selectCollection('users');
    
    // Explicitly test connection
    $client->listDatabases();
} catch (Exception $e) {
    die("<div style='color: #fca5a5; background: #450a0a; padding: 20px; border-radius: 10px; font-family: sans-serif; border: 1px solid #7f1d1d;'>
            <strong>MongoDB Atlas Connection Error:</strong><br>" . 
            htmlspecialchars($e->getMessage()) . 
            "<br><br><strong>Tip:</strong> Ensure you have the 'mongodb' extension enabled in php.ini.
         </div>");
}
?>
