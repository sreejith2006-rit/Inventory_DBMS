<?php
include '../config/db.php';

$message = "";
$error = "";

$categories = $pdo->query("SELECT Category_ID, Category_Name FROM CATEGORY ORDER BY Category_Name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $min_stock = $_POST['min_stock'] ?? '';

    if ($item_name === '' || $category_id === '' || $min_stock === '') {
        $error = "All fields are required.";
    } elseif ($min_stock < 0) {
        $error = "Minimum stock cannot be negative.";
    }
    else {
        try {
            $stmt = $pdo->prepare("INSERT INTO ITEM (Item_Name, Category_ID, Min_Stock) 
                                 VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE 
                                 Category_ID = VALUES(Category_ID), 
                                 Min_Stock = VALUES(Min_Stock)");
            $stmt->execute([$item_name, $category_id, $min_stock]);
            
            if ($stmt->rowCount() == 2) {
                $message = "Existing item '" . htmlspecialchars($item_name) . "' updated successfully.";
            } else {
                $message = "New item '" . htmlspecialchars($item_name) . "' added successfully.";
            }
        } catch (PDOException $e) {
            $error = "Failed to save item: " . $e->getMessage();
        }
    }
}

// Fetch existing items
$existing_items = $pdo->query("
    SELECT i.Item_ID, i.Item_Name, c.Category_Name, i.Min_Stock 
    FROM ITEM i 
    LEFT JOIN CATEGORY c ON i.Category_ID = c.Category_ID
    ORDER BY i.Item_Name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<h2 class="page-title mb-3">Add Item</h2>
<p class="text-muted">Add a new inventory item.</p>

<div class="card p-4 form-card">
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" name="item_name" class="form-control" placeholder="Enter item name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['Category_ID']; ?>">
                        <?php echo htmlspecialchars($category['Category_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Minimum Stock</label>
            <input type="number" name="min_stock" class="form-control" placeholder="Enter minimum stock" min="0" required>
        </div>

        <button type="submit" class="btn btn-success">Save Item</button>
    </form>
</div>

<div class="card p-4 mt-4 form-card">
    <h5 class="mb-3">Existing Items</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Min Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($existing_items) > 0): ?>
                    <?php foreach ($existing_items as $item): ?>
                        <tr>
                            <td><?php echo $item['Item_ID']; ?></td>
                            <td><?php echo htmlspecialchars($item['Item_Name']); ?></td>
                            <td><?php echo htmlspecialchars($item['Category_Name'] ?? 'Uncategorized'); ?></td>
                            <td><?php echo $item['Min_Stock']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>