# 📘 Inventory Management System: Functional Module Handbook

This handbook provides a deep technical and functional walkthrough of every module and page within the Inventory Management System. It explains how each component operates, the database interactions involved, and the business rules enforced.

---

## 📈 1. The Global Overview (Dashboard)

File: `index.php`

The Dashboard is the system's real-time command center, integrating data from across all MySQL tables to provide actionable insights.

### Technical Metrics Logic:
*   **Total Items**: `SELECT COUNT(*) FROM ITEM`
*   **Current Stock**: `SELECT SUM(Quantity) FROM BATCH` (Aggregated across all batches)
*   **Near Expiry Alerts**: Calculations compare the current system date with the `Expiry_Date` in the `batch` table:
    ```sql
    WHERE b.Expiry_Date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ```
*   **Low Stock Alerts**: Computed by joining the `item` table with aggregated batch quantities and checking `SUM(Quantity) < Min_Stock`.

---

## 🏬 2. Master Data Management

### 🏷️ Category Management
File: `pages/add_category.php`
*   **Function**: Define the high-level taxonomy of the inventory.
*   **Security**: Prevents duplicate category names via a MySQL `UNIQUE` constraint.

### 🍱 Item Master Management
File: `pages/add_item.php`
*   **Smart Logic**: Uses `INSERT ... ON DUPLICATE KEY UPDATE` to allow for seamless updates of existing items instead of throwing a database error.
*   **Min_Stock Attribute**: A critical field for automated low-stock warnings on the dashboard.

### 🏭 Supplier & Contact Management
File: `pages/add_supplier.php`
*   **Function**: Manage the vendor directory.
*   **AJAX Integration**: The page uses specialized JavaScript to auto-fetch existing emails when a name is entered:
    ```javascript
    fetch('add_supplier.php?action=get_supplier&name=' + encodeURIComponent(name))
        .then(response => response.json())
        .then(data => {
            if (data.found) {
                // Supplier exists, seal the email field
                emailInput.value = data.email || '';
                emailInput.readOnly = true;
            }
        });
    ```
*   **Multi-Phone Support**: Supports adding multiple phone numbers for the same supplier via the `supplier_phone` relational table.

---

## 📦 3. Procurement & Batching

### 🧪 Smart Batch Management
File: `pages/add_batch.php`
*   **Function**: Registers specific lots of inventory with manufacturing and expiry details.
*   **Batch Update Logic**: If a batch with the same manufacturing/expiry dates is re-added, the system automatically increments the quantity:
    ```sql
    UPDATE BATCH SET Quantity = Quantity + ? WHERE Batch_ID = ?
    ```

---

## 💰 4. Sales & Inventory Depletion

File: `pages/make_sale.php`

The sales module is the most critical logic engine in the application.

### The Sales Workflow:
1.  **Atomic Transactions**: Before allowing a sale, the system executes a `FOR UPDATE` lock on the batch record to prevent simultaneous transactions from overselling stock:
    ```sql
    SELECT Quantity FROM BATCH WHERE Batch_ID = ? FOR UPDATE
    ```
2.  **Expiry Blocking**: Even if a user chooses an expired batch, the database trigger (`trg_check_expiry`) will block the sale.

---

## 📊 5. Reporting & Business Intelligence

File: `pages/reports.php`

The reporting module generates four distinct, print-ready reports:

1.  **Current Stock Report**: Displays item master data along with real-time aggregated quantities.
2.  **Near Expiry Report**: Lists specific batches that require immediate sale or disposal (Next 7 days).
3.  **Expired Stocks Report**: Identification of inventory that must be removed for safety.
4.  **Supplier Performance**: Counts total purchases per vendor to aid in negotiation.

---

## 🎨 6. Global Assets (Partials)

*   **`header.php`**: MANAGES ROOT PATHS. A specialized PHP snippet dynamically calculates `$root_path` to ensure that CSS and assets load correctly from any subfolder.
*   **`sidebar.php`**: THE ACCESS CONTROLLER. Dynamically renders the navigation menu based on the user's role (`$_SESSION['role']`).

---
*End of Functional Handbook.*
