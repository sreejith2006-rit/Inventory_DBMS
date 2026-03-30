# 🎨 Inventory Management System: UI/UX & Design Manual

This manual provides a detailed technical explanation of the design language, component architecture, and visualization logic used in the Inventory Management System.

---

## 🏛️ 1. The Design Language

The system uses a **Modern Professional** design language, focusing on clarity, responsiveness, and visual hierarchy.

### 🎨 Color Palette & CSS Variables
The system uses the following core hex codes, defined as semantic markers in `assets/css/style.css`:
*   **Primary Navy** (`#0f172a`): Used for the sidebar and high-level headings.
*   **Background Gray** (`#f8fafc`): Provides a clean, neutral canvas for data cards.
*   **Accent Success** (`#22c55e`): Represents health, safe stock, and completions.
*   **Accent Warning** (`#f59e0b`): Highlights near-expiry items and cautions.
*   **Accent Danger** (`#ef4444`): Indicates expired stock, low stock, and logout actions.

### 🖋️ Typography
The application utilizes the **Inter** typeface via Google Fonts. This ensures maximum readability across different screen resolutions and provides a sleek, modern aesthetic.

---

## 🏗️ 2. Component Architecture

The UI is built using a **Partial-Based Architecture**, allowing for consistent headers and navigation across the entire system.

### `header.php`
The system's master entry point. It manages:
*   Bootstrap 5 and Bootstrap Icons integration.
*   **Dynamic Asset Loading**: Uses a PHP `strpos` check on `$_SERVER['PHP_SELF']` to calculate the relative path to CSS files, ensuring style consistency deep within subdirectories.

### `sidebar.php`
The primary navigation engine. It uses a **Flexbox** layout to remain pinned to the left on desktop and provides the core access control logic (RBAC).

### Glassmorphism & Cards
The dashboard and forms utilize a **Glassmorphism** effect:
*   Subtle borders (`rgba(0,0,0,0.05)`).
*   Soft shadows.
*   12px border radius for a "premium" software feel.

---

## 📊 3. Data Visualization (Chart.js)

The NKR system integrates the **Chart.js v4** library to transform raw MySQL data into actionable business intelligence.

### 📉 The Bar Chart (Stock Levels)
*   **Source**: Total stock per item ID.
*   **Customization**: Uses a sky-blue fill with a dark stroke for clarity.
*   **Interaction**: Includes hover-responsive tooltips to show exact quantities.

### 🍩 The Doughnut Chart (Expiry Distribution)
*   **Source**: A specialized SQL query that counts Safe, Near-Expiry, and Expired batches.
*   **Categorization**:
    *   **Green**: Safe Stock.
    *   **Yellow**: Near-Expiry (7-day window).
    *   **Red**: Expired items.
*   **Logic**: Located in `index.php`, this chart is the first visual an administrator sees, enabling immediate intervention.

### 🖨️ Print Optimization
The application includes a specialized media query (`@media print`) in `style.css` that:
1.  Hides the sidebar and navigation.
2.  Ensures tables expand to 100% width.
3.  Removes background colors and shadows to save ink and provide a professional, white-paper look for stakeholders.

---
*End of UI/UX Manual.*
