# 📦 Inventory & Expiry Tracking System

A professional, web-based inventory management solution built with PHP and MySQL. This system helps businesses manage stock levels, track product batches, and monitor expiry dates with ease.

## ✨ Key Features

*   **📊 Insightful Dashboard:** Real-time visualization of inventory levels and batch status using Chart.js.
*   **🗓️ Expiry Tracking:** Automated highlighting of near-expiry and expired products.
*   **📦 Batch Management:** Add and track specific batches with manufacturing and expiry date validation.
*   **📉 Low Stock Alerts:** Visual indicators for items falling below their minimum stock threshold.
*   **🖨️ Professional Reports:** Clean, print-optimized reports for stock, expiry, and supplier summaries.
*   **🛡️ Data Integrity:** Built-in safeguards against duplicate items and invalid date ranges.

## 🚀 Getting Started

### Prerequisites
*   [XAMPP](https://www.apachefriends.org/) (PHP 7.4+ and MySQL)
*   Web Browser (Chrome, Firefox, or Edge)

### Installation
1.  Clone or download this repository to your XAMPP `htdocs` folder.
2.  **Database Setup:**
    *   Open **phpMyAdmin**.
    *   Create a new database named `inventory_system`.
    *   Run the initial SQL setup (provided in your project docs) to create the tables.
3.  **Configuration:**
    *   Navigate to the `config/` folder.
    *   Rename `db_example.php` to `db.php`.
    *   Open `db.php` and enter your local database credentials (usually `root` with no password).

### Database Constraints (Recommended)
To ensure the best performance and data safety, run these queries in your phpMyAdmin SQL tab:
```sql
-- Prevent duplicate items
ALTER TABLE ITEM ADD UNIQUE (Item_Name);

-- Enforce valid date ranges for batches
ALTER TABLE BATCH ADD CONSTRAINT check_dates CHECK (Manufacturing_Date < Expiry_Date);
```

## 💻 Usage

1.  **Dashboard:** Get an instant overview of your total items, near-expiry batches, and low-stock alerts.
2.  **Add Item:** Define your product master records and set minimum stock thresholds.
3.  **Add Batch:** Link stock to a specific purchase and set its expiry date.
4.  **Reports:** View and print detailed summaries for your stakeholders.

## 🛠️ Built With
*   **Frontend:** HTML5, Vanilla CSS, Bootstrap 5, Chart.js.
*   **Backend:** PHP (PDO for secure database interactions).
*   **Icons:** Bootstrap Icons.

---
*Created with ❤️ for professional inventory management.*
