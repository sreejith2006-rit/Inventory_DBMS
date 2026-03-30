# ⚙️ Inventory Management System: Installation & Environment Manual

This manual provides a detailed technical guide for setting up the Inventory Management System on a local development server (XAMPP/Apache) and connecting it to a **MongoDB Atlas** cloud environment.

---

## 🛠️ 1. Prerequisites & Stack

To ensure the system operates at maximum efficiency, the following environment is required:
*   **Web Server**: Apache (via XAMPP).
*   **Database 1**: MySQL/MariaDB (v10.4+) — For Inventory data.
*   **Database 2**: MongoDB Atlas (v6.0+) — For Security data.
*   **PHP Version**: PHP 8.2+ with the following extensions enabled:
    *   `pdo_mysql`
    *   `openssl` (for secure Atlas connection)
    *   `mongodb` (to interact with the NoSQL layer)

---

## 🏗️ 2. Step-by-Step Local Setup

### Step 1: File Deployment
1.  Navigate to your XAMPP installation folder (usually `C:\xampp\htdocs`).
2.  Clone or download the project into a folder named `inventory_project`.

### Step 2: MySQL Database Initialization
1.  Start the XAMPP Control Panel and launch **Apache** and **MySQL**.
2.  Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3.  Create a new database named **`inventory_system`**.
4.  Import the `inventory_system.sql` file provided in the repository root.
5.  Rename `config/db_example.php` to `config/db.php` and enter your credentials.

### Step 3: PHP Configuration (Crucial)
To connect to MongoDB Atlas, you **MUST** enable the OpenSSL and MongoDB extensions:
1.  Open `C:\xampp\php\php.ini`.
2.  Search for `;extension=openssl` and remove the semicolon.
3.  Search for `;extension=mongodb` (if present) or add `extension=mongodb` to the section.
4.  **Restart Apache** from the XAMPP Control Panel.

---

## 🌩️ 3. MongoDB Atlas Configuration

The NKR system uses Atlas for session security and user roles.

### Step 1: Atlas Cluster Creation
1.  Create a free tier cluster on [mongodb.com](https://www.mongodb.com/).
2.  Create a database user and a cluster-wide password.
3.  Whitelist your local IP address in the **Network Access** tab.

### Step 2: Connection String Integration
1.  Open `config/mongo_config.php`.
2.  Enter your **SRV Connection String** into the `$uri` variable.
3.  Ensure the URI includes your username and password.

### Step 3: SSL Certificate Handling
The system includes `cacert.pem` in the root directory. This is used by the `mongo_config.php` file to verify the SSL identity of the Atlas cluster. **Do not remove this file.**

---

## 🛡️ 4. Seeding the Initial Admin

Once both databases are connected, you must create the foundational user:
1.  Navigate to [http://localhost/inventory_project/create_admin.php](http://localhost/inventory_project/create_admin.php).
2.  The script will check if an admin already exists. If not, it will create one.
3.  **Default Credentials**:
    *   **Username**: `admin`
    *   **Password**: `admin123`

---

## 🧪 5. Troubleshooting & FAQ

### "Class 'MongoDB\Client' not found"
*   **Cause**: The MongoDB PHP extension is not loaded.
*   **Fix**: Check `php.ini` and ensure the `extension=mongodb` line is present and Apache was restarted.

### "SSL: Handshake failed"
*   **Cause**: Apache cannot find the CA certificate provider.
*   **Fix**: Verify that `cacert.pem` is in the root directory and that the `tlsCAFile` path in `mongo_config.php` is correct.

### "Access Denied for user 'root'@'localhost'"
*   **Cause**: MySQL credentials in `config/db.php` are incorrect.
*   **Fix**: Match the credentials to your XAMPP MySQL settings (usually user `root` with no password).

---
*End of Installation Manual.*
