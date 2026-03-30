<?php
session_start();

// Protection: Redirect to login if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php?error=Access Denied. Please login first.");
    exit;
}

// RBAC: Only allow admin to add users
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?error=Unauthorized access. Admin role required.");
    exit;
}

require_once __DIR__ . '/../config/mongo_config.php';
use MongoDB\BSON\UTCDateTime;

$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'staff');

    if (!empty($username) && !empty($email) && !empty($password)) {
        try {
            // Check if username already exists
            $existingUser = $usersCollection->findOne(['username' => $username]);

            if ($existingUser) {
                $message = "Error: Username '$username' already exists.";
                $messageClass = "alert-danger";
            } else {
                // Insert new user
                $result = $usersCollection->insertOne([
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'status' => 'active',
                    'created_at' => new \MongoDB\BSON\UTCDateTime()
                ]);

                if ($result->getInsertedCount()) {
                    $message = "User '$username' created successfully!";
                    $messageClass = "alert-success";
                }
            }
        } catch (Exception $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageClass = "alert-danger";
        }
    } else {
        $message = "Please fill in all required fields.";
        $messageClass = "alert-warning";
    }
}

include '../partials/header.php';
include '../partials/sidebar.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h2 class="page-title mb-4">Add New User</h2>

            <?php if ($message): ?>
                <div class="alert <?php echo $messageClass; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="add_user.php">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Enter temporary password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">User Role</label>
                    <select name="role" class="form-select">
                        <option value="staff">Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary p-2 fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
