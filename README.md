# 📦 Inventory & Expiry Tracking System

A professional, web-based inventory management solution built with **PHP**, **MySQL**, and **MongoDB Atlas**. This system helps businesses manage stock levels, track product batches, and monitor expiry dates with ease while maintaining top-tier security.

---

## ✨ Key Features

### 🏢 Inventory & Reporting (SQL Powered)
*   **📊 Insightful Dashboard:** Real-time visualization of inventory levels and batch status using Chart.js.
*   **🗓️ Expiry Tracking:** Automated highlighting of near-expiry and expired products.
*   **📦 Batch Management:** Add and track specific batches with manufacturing and expiry date validation.
*   **📉 Low Stock Alerts:** Visual indicators for items falling below their minimum stock threshold.
*   **🖨️ Professional Reports:** Clean, print-optimized reports for stock, expiry, and supplier summaries.
*   **🛡️ Data Integrity:** Built-in safeguards against duplicate items and invalid date ranges.

### 🔐 Security & Access (MongoDB Atlas Powered)
*   **🔒 Secure Authentication:** Modern login system integrated with **MongoDB Atlas**, featuring Bcrypt password hashing.
*   **🛡️ Role-Based Access Control (RBAC):** Distinct permissions for **Admin** and **Staff**.
*   **🧑‍🤝‍🧑 User Management:** Built-in tools for Administrators to securely add and manage new team members.

---

## 🚀 Getting Started

### Prerequisites
*   [XAMPP](https://www.apachefriends.org/) (PHP 7.4+ and MySQL)
*   **MongoDB PHP Extension** (Enabled in `php.ini`)
*   **MongoDB Atlas Cluster** (For secure user authentication)
*   Web Browser (Chrome, Firefox, or Edge)

### Installation & Setup

1.  **Project Files:** Download or clone this repository to your XAMPP `htdocs` folder.
2.  **MySQL setup:**
    *   Open **phpMyAdmin** and create a database named `inventory_system`.
    *   Import the `inventory_system.sql` file provided in the root directory.
    *   Rename `config/db_example.php` to `config/db.php` and enter your local MySQL credentials.
3.  **MongoDB Atlas config:**
    *   Create a cluster on [MongoDB Atlas](https://www.mongodb.com/atlas).
    *   Open `config/mongo_config.php` and enter your **Atlas Connection String**.
4.  **SSL Configuration:**
    *   Ensure `cacert.pem` is in the project root.
    *   Ensure `extension=openssl` is uncommented in your `php.ini`.
5.  **Seed Admin account:**
    *   Visit `http://localhost/inventory_project/create_admin.php` once to create the initial **Admin** account (`admin` | `admin123`).

---

## 💻 Usage

1.  **Dashboard:** Get an instant overview of your total items, near-expiry batches, and low-stock alerts.
2.  **Add Item:** Define your product master records and set minimum stock thresholds.
3.  **Add Batch:** Link stock to a specific purchase and set its expiry date.
4.  **Reports:** View and print detailed summaries for your stakeholders.
5.  **User Roles:** 
    *   **Admin:** Full access to all features and user management.
    *   **Staff:** Restricted access to inventory tasks (cannot manage other users).

---

## 🛠️ Built With
*   **Frontend:** HTML5, Vanilla CSS, Bootstrap 5, Chart.js.
*   **Backend:** PHP (PDO for MySQL, MongoDB PHP Library for Atlas).
*   **Database:** MySQL (Inventory data) & MongoDB Atlas (User data).
*   **Icons:** Bootstrap Icons & Font Awesome.

---
*Created with ❤️ for professional and secure inventory management.*
