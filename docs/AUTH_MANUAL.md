# 🔒 Inventory Management System: Authentication & RBAC Manual

This manual provides a detailed technical explanation of the **Hybrid Security Architecture** used in the Inventory Management System. It covers the integration between the PHP backend and the **MongoDB Atlas** NoSQL database for user authentication and role-based access control (RBAC).

---

## 🏗️ 1. The Hybrid Database Strategy

While the core inventory business data is stored in **MySQL**, the system uses **MongoDB Atlas** for its security layer. 

### Why Hybrid?
*   **Decoupling**: Business logic (Inventory) is separated from security logic (Users).
*   **Scalability**: MongoDB Atlas provides world-class security, encryption at rest, and easy scaling for user sessions.
*   **NoSQL Flexibility**: User documents can store diverse attributes (like roles, activity logs, or contact details) without needing complex schema migrations.

---

## 🌩️ 2. MongoDB Atlas Configuration

The user data is stored in a MongoDB Atlas cluster, and the connection is established via the **MongoDB PHP Library**.

### Cluster Credentials
*   **Database**: `inventory_system`
*   **Collection**: `users`
*   **Security Integration**: The system uses `cacert.pem` (Certificate Authority) to establish a secure, encrypted TLS/SSL connection between the local Apache server and the cloud Atlas cluster.

### User Document Structure
Every user record in the `users` collection is stored as a BSON document:
```json
{
  "_id": "60f7a6e1...",
  "username": "admin",
  "email": "admin@inventory.com",
  "password_hash": "$2y$10$...",
  "role": "admin",
  "status": "active",
  "created_at": "2026-03-30T17:00:00Z"
}
```

---

## 🔑 3. Authentication & Session Flow

The `login_process.php` file manages the core authentication handshake.

### The Handshake Steps:
1.  **Sanitization**: The incoming username and password from the login form are trimmed and sanitized.
2.  **Database Lookup**: The system queries MongoDB Atlas for a user document where the `username` matches.
3.  **Password Verification**: 
    ```php
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true); 
        $_SESSION['logged_in'] = true;
        // ... set other session variables
    }
    ```
4.  **Session Initiation**: Upon success, a session is started.
5.  **Regeneration**: `session_regenerate_id(true)` is called immediately after login to prevent **Session Fixation attacks**.

### Session Variables
Once logged in, the following persistent tokens are stored in `$_SESSION`:
*   `$_SESSION['logged_in']` (bool): Master flag for page protection.
*   `$_SESSION['username']` (string): Display name.
*   `$_SESSION['role']` (string): The critical token for **RBAC**.

---

## 🛡️ 4. Role-Based Access Control (RBAC) Logic

The system identifies two distinct roles: **Admin** and **Staff**.

### Implementation of RBAC
The system enforces RBAC at two levels:
1.  **UI Level**: In `sidebar.php`, the "Add User" link is wrapped in a PHP check:
    ```php
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/add_user.php">Add User</a>
        </li>
    <?php endif; ?>
    ```
2.  **Page Level**: At the very top of restricted pages (like `add_user.php`), the following check is executed:
    ```php
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../index.php?error=Unauthorized access. Admin role required.");
        exit;
    }
    ```

---

## 🚪 5. Secure Logout Mechanism

The `logout.php` file ensures a complete cleanup of the authentication state.
1.  **Session Destruction**: `session_destroy()` is called to invalidate the server-side state.
2.  **Cookie Cleanup**: The session cookie is cleared from the user's browser.
3.  **Redirection**: The user is safely returned to the login screen with a success message.

---
*End of Authentication Manual.*
