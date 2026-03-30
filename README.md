# 📦 Inventory & Expiry Tracking System

A professional, web-based inventory management solution built with **PHP**, **MySQL**, and **MongoDB Atlas**. This system provides a robust way to manage stock levels, track product batches, and monitor expiry dates while ensuring top-tier security for your data.

## ✨ Key Features

*   **📊 Insightful Dashboard:** Real-time visualization of inventory levels and batch status using Chart.js, powered by a secure session-based original SQL dashboard.
*   **🔒 Secure Authentication:** Modern login system integrated with **MongoDB Atlas**, featuring password hashing and secure session management.
*   **🛡️ Role-Based Access Control (RBAC):** Distinct permissions for **Admin** and **Staff**. Admins can manage users, while Staff focuses on inventory tasks.
*   **🧑‍🤝‍🧑 User Management:** Built-in tools for Administrators to securely add and manage new team members.
*   **🗓️ Expiry Tracking:** Automated highlighting of near-expiry and expired products.
*   **📦 Batch Management:** Add and track specific batches with manufacturing and expiry date validation.
*   **📉 Low Stock Alerts:** Visual indicators for items falling below their minimum stock threshold.
*   **🖨️ Professional Reports:** Clean, print-optimized reports for stock, expiry, and supplier summaries.

## 🚀 Getting Started

### Prerequisites
*   [XAMPP](https://www.apachefriends.org/) (PHP 7.4+ and MySQL)
*   **MongoDB PHP Extension** (Enabled in `php.ini`)
*   **MongoDB Atlas Cluster** (For secure user authentication)
*   Web Browser (Chrome, Firefox, or Edge)

### Installation & Setup

1.  **Clone the Project:** Download or clone this repository to your XAMPP `htdocs` folder.
2.  **MySQL Database Setup (Dashboard Data):**
    *   Open **phpMyAdmin** and create a database named `inventory_system`.
    *   Import the `inventory_system.sql` file provided in the root directory.
    *   Rename `config/db_example.php` to `config/db.php` and enter your local MySQL credentials.
3.  **MongoDB Atlas Setup (Authentication):**
    *   Create a cluster on [MongoDB Atlas](https://www.mongodb.com/atlas).
    *   Open `config/mongo_config.php` and paste your **Atlas Connection String** into the `$uri` variable.
4.  **SSL/TLS Security:**
    *   Ensure `cacert.pem` is in your project root.
    *   Ensure `extension=openssl` is uncommented in your `php.ini`.
5.  **Seed Admin User:**
    *   Navigate to `http://localhost/inventory_project/create_admin.php` in your browser once to create the initial **Admin** account.
    *   **Default Admin:** `admin` | **Password:** `admin123`

## 💻 Usage

1.  **Login:** Access the system through `login.php`.
2.  **Admin Tasks:** Admins have exclusive access to the **"Add User"** button in the sidebar.
3.  **Staff Tasks:** Staff members can view the dashboard and manage inventory but cannot manage other users.
4.  **Logout:** Use the red **Logout** button at the top-right corner to securely end your session.

## 🛠️ Built With
*   **Frontend:** HTML5, CSS Grid/Flexbox, Bootstrap 5, Chart.js.
*   **Backend:** PHP (PDO for MySQL, MongoDB PHP Library for Atlas).
*   **Database:** MySQL (Inventory data) & MongoDB Atlas (User data).
*   **Security:** PHP Sessions, Bcrypt Password Hashing, OpenSSL TLS/SSL.

---
*Created with ❤️ for professional and secure inventory management.*
