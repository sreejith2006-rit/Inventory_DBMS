# 📊 Inventory Management System: Database Technical Manual

This document provides a deep-dive, field-by-field explanation of the relational database architecture that powers the Inventory Management System. It covers the schema design, normalization strategies, and automated logic embedded in the database.

---

## 🏗️ 1. Relational Schema Overview

The system uses a **MariaDB/MySQL** relational database named `inventory_system`. The schema is designed for **Third Normal Form (3NF)** to ensure data consistency and reduce redundancy.

### 🗺️ Entity-Relationship (ER) logic
*   **Categories** contain many **Items**.
*   **Suppliers** provide many **Purchases**.
*   **Purchases** are linked to many **Batches**.
*   **Items** are linked to many **Batches**.
*   **Batches** are linked to many **Sales**.

---

## 🗃️ 2. Detailed Table Definitions

### 🏷️ `category` Table
Stores the high-level classifications for inventory items.
*   **`Category_ID`** (int, PK, AI): Unique identifier for the category.
*   **`Category_Name`** (varchar, Unique): The descriptive name (e.g., "Dairy").

### 🏬 `supplier` Table
Maintains the master records for vendors.
*   **`Supplier_ID`** (int, PK, AI): Unique identifier for the vendor.
*   **`Supplier_Name`** (varchar): Business name.
*   **`Email`** (varchar, Unique): Primary contact email.

### 📞 `supplier_phone` Table
A specialized bridge table to handle the **Multi-valued Attribute** of phone numbers.
*   **`Supplier_ID`** (int, FK): Links back to the `supplier` table.
*   **`Phone_No`** (varchar, 15): The specific contact number.
*   **Primary Key**: Composite of `(Supplier_ID, Phone_No)`.

### 📦 `item` Table
The master record for individual products.
*   **`Item_ID`** (int, PK, AI): Unique identifier for the item.
*   **`Item_Name`** (varchar, Unique): The product name.
*   **`Category_ID`** (int, FK): Links to the `category` table.
*   **`Min_Stock`** (int, Default: 0): The threshold for "Low Stock" alerts.

### 🧾 `purchase` Table
Logs procurement events.
*   **`Purchase_ID`** (int, PK, AI): Unique identifier for the order.
*   **`Supplier_ID`** (int, FK): Links to the `supplier` who provided the goods.
*   **`Purchase_Date`** (date): When the goods were received.

### 🧪 `batch` Table
The most dynamic table in the system, tracking inventory at the "lot" level.
*   **`Batch_ID`** (int, PK, AI): Unique identifier for the batch.
*   **`Item_ID`** (int, FK): The specific item in this batch.
*   **`Purchase_ID`** (int, FK): The order this batch arrived with.
*   **`Quantity`** (int): Current available stock in this specific batch.
*   **`Manufacturing_Date`** (date): When the items were produced.
*   **`Expiry_Date`** (date): The critical date used for tracking perishable goods.

### 💰 `sale` Table
Records individual transaction events.
*   **`Sale_ID`** (int, PK, AI): Unique record for the sale.
*   **`Batch_ID`** (int, FK): The specific batch from which the items were sold.
*   **`Quantity_Sold`** (int): The amount deducted from stock.
*   **`Sale_Date`** (date): Transaction timestamp.

---

## ⚡ 3. Automated Trigger Logic

The system utilizes heavy-duty SQL triggers to enforce business rules directly at the data layer, ensuring that even if the PHP code is bypassed, the data remains consistent.

### 🚫 `trg_check_expiry` (BEFORE INSERT on `sale`)
**Logic**: Before a sale is recorded, the trigger fetches the `Expiry_Date` from the related `batch`. If the `Sale_Date` is greater than the `Expiry_Date`, it raises a `SIGNAL SQLSTATE '45000'`, effectively blocking the transaction.

### 📉 `trg_check_stock` (BEFORE INSERT on `sale`)
**Logic**: Compares the `Quantity_Sold` in the new sale record against the `Quantity` available in the `batch` table.

```sql
CREATE TRIGGER trg_check_stock BEFORE INSERT ON sale 
FOR EACH ROW BEGIN
    DECLARE available_qty INT;
    SELECT Quantity INTO available_qty FROM BATCH WHERE Batch_ID = NEW.Batch_ID;
    IF NEW.Quantity_Sold > available_qty THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Sale not allowed: insufficient stock.';
    END IF;
END;
```

### 🔄 `trg_update_batch_after_sale` (AFTER INSERT on `sale`)
**Logic**: Once a sale is successful, this trigger automatically subtracts the `Quantity_Sold` from the `batch` table.

```sql
CREATE TRIGGER trg_update_batch_after_sale AFTER INSERT ON sale 
FOR EACH ROW BEGIN
    UPDATE BATCH SET Quantity = Quantity - NEW.Quantity_Sold WHERE Batch_ID = NEW.Batch_ID;
END;
```

---

## 🛡️ 4. Data Integrity & Constraints

*   **Foreign Key Cascades**: All foreign keys use `ON UPDATE CASCADE` to ensure that if a master ID (like `Item_ID`) changes, the related batches and sales are updated automatically.
*   **UNIQUE Constraints**: Prevent duplicate item names (Item Master) and duplicate category names, ensuring a clean and searchable database.
*   **Date Integrity**: The application layer enforces `Manufacturing_Date < Expiry_Date` before the record even hits the database.

---
*End of Database Manual.*
