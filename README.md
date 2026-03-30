# 📦 Inventory Management System

**A professional, enterprise-grade inventory management solution built with a hybrid SQL/NoSQL infrastructure.**

The **Inventory Management System** is designed for businesses that require precision in tracking perishable stocks, automated expiry alerts, and secure, role-based user access. By leveraging the relational power of **MySQL** for data integrity and the modern scalability of **MongoDB Atlas** for authentication, this system provides a stable and high-performance environment for modern inventory needs.

---

## 🌟 Key Application Features

### 🏢 Full-Lifecycle Stock Management
*   **📊 Insightful Dashboard**: Real-time aggregation of total items, upcoming expiries, and low-stock alerts using **Chart.js** visualizations.
*   **🗓️ Expiry Protection**: Automated "First-Expiry, First-Out" tracking with built-in SQL triggers to block the sale of expired inventory.
*   **📦 Multi-Batch Tracking**: Support for managing the same item across different batches, each with unique manufacturing and expiry dates.
*   **📉 Intelligent Stock Alerts**: Automated visual indicators for items falling below their minimum stock threshold.
*   **🖨️ Professional Reports**: Print-optimized, clean reports for current stock, near-expiry schedules, and supplier-wise summary data.

### 🔐 Advanced Security & RBAC
*   **🔒 MongoDB Atlas Integration**: High-security authentication layer using cloud-based NoSQL for user credentials.
*   **🛡️ Role-Based Access Control (RBAC)**: Distinct permissions for **Admin** and **Staff** roles, ensuring sensitive management features are protected.
*   **🧑‍🤝‍🧑 User Management Portal**: A dedicated administrative dashboard for adding, managing, and securing user accounts with **Bcrypt** hashing.

---

## 📖 Comprehensive Project Manuals

For a deep-dive into the technical and functional logic of this application, please refer to our **Super-Detailed Manuals** (Explanatory depth up to 100,000 words):

### 🛠️ [1. Technical & Database Manual](docs/DATABASE_MANUAL.md)
*Field-by-field schema guide, SQL Trigger logic, and Relational ER strategies.*

### 🔑 [2. Authentication & Security Manual](docs/AUTH_MANUAL.md)
*MongoDB Atlas integration, Session token security, and RBAC implementation.*

### 📘 [3. Functional Module Handbook](docs/MODULE_HANDBOOK.md)
*Detailed walkthrough of the Purchase, Sale, Inventory, and Reporting modules.*

### 🎨 [4. UI/UX & Design Manual](docs/UI_UX_MANUAL.md)
*CSS design tokens, UX component architecture, and Data Visualization logic.*

### ⚙️ [5. Installation & Environment Guide](docs/INSTALL_MANUAL.md)
*Step-by-step setup for XAMPP, PHP extensions (OpenSSL/MongoDB), and Atlas.*

---

## 🚀 Quick Local Setup

1.  **Deploy**: Clone to `htdocs/inventory_project`.
2.  **MySQL**: Create `inventory_system` DB and import `inventory_system.sql`.
3.  **Config**: Rename `config/db_example.php` to `config/db.php`.
4.  **Atlas**: Enter your connection string in `config/mongo_config.php`.
5.  **Seed**: Run `create_admin.php` to generate your login.
    *   **Admin**: `admin` | **Pass**: `admin123`

---
*Developed with ❤️ for high-precision inventory and security management.*
