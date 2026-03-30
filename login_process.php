<?php
session_start();
require_once __DIR__ . '/config/mongo_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Please fill in all fields");
        exit;
    }

    try {
        // Find user by username
        $user = $usersCollection->findOne(['username' => $username]);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Regeneration of session ID for security (prevent fixation)
            session_regenerate_id(true);
            
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = (string)$user['username'];
            $_SESSION['user_id'] = (string)$user['_id'];
            $_SESSION['role'] = (string)$user['role'];

            header("Location: index.php");
            exit;
        } else {
            header("Location: login.php?error=Invalid username or password");
            exit;
        }
    } catch (Exception $e) {
        // Log the error in a real app, here we show it for debugging
        header("Location: login.php?error=Database error: " . urlencode($e->getMessage()));
        exit;
    }
} else {
    // If someone tries to access this file directly via GET
    header("Location: login.php");
    exit;
}
?>