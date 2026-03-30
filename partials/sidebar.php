<div class="sidebar p-4">
    <div class="text-center mb-4">
        <h4 class="text-white mb-0">Inventory System</h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/index.php">Dashboard</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/add_category.php">Add Category</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/add_item.php">Add Item</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/add_supplier.php">Add Supplier</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/add_purchase.php">Add Purchase</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/add_batch.php">Add Batch</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/make_sale.php">Make Sale</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/reports.php">Reports</a>
        </li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="/inventory_project/pages/add_user.php">Add User</a>
        </li>
        <?php endif; ?>
    </ul>
</div>

<div class="content flex-grow-1 p-4">
    <div class="d-flex justify-content-end mb-4 d-print-none">
        <a href="/inventory_project/logout.php" class="btn btn-danger shadow-sm">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </div>