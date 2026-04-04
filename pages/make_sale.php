<?php
include '../config/db.php';

$message = "";
$error = "";

$batches = $pdo->query("
    SELECT b.Batch_ID, i.Item_Name, b.Quantity, b.Expiry_Date
    FROM BATCH b
    JOIN ITEM i ON b.Item_ID = i.Item_ID
    ORDER BY b.Batch_ID DESC
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_id = $_POST['batch_id'] ?? '';
    $sale_date = $_POST['sale_date'] ?? '';
    $quantity_sold = $_POST['quantity_sold'] ?? '';

    if ($batch_id === '' || $sale_date === '' || $quantity_sold === '') {
        $error = "All fields are required.";
    } elseif ($quantity_sold <= 0) {
        $error = "Quantity sold must be a positive number.";
    } else {
        try {
            $pdo->beginTransaction();

            // Lock and check current batch stock
            $stmt_check = $pdo->prepare("SELECT Quantity FROM BATCH WHERE Batch_ID = ? FOR UPDATE");
            $stmt_check->execute([$batch_id]);
            $batch_row = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$batch_row) {
                throw new Exception("Batch not found.");
            }

            // Insert into SALE 
            // Note: The following SQL Triggers execute automatically when this INSERT happens:
            // 1. trg_check_expiry (BEFORE INSERT): Blocks sale if the batch is expired.
            // 2. trg_check_stock (BEFORE INSERT): Blocks sale if requested quantity > available quantity.
            // 3. trg_update_batch_after_sale (AFTER INSERT): Deducts the sold quantity from the batch table.
            $stmt_insert = $pdo->prepare("INSERT INTO SALE (Batch_ID, Quantity_Sold, Sale_Date) VALUES (?, ?, ?)");
            $stmt_insert->execute([$batch_id, $quantity_sold, $sale_date]);

            $pdo->commit();
            $message = "Sale completed successfully. Inventory stock has been updated.";

            // Refresh batch list to show new quantities
            $batches = $pdo->query("
                SELECT b.Batch_ID, i.Item_Name, b.Quantity, b.Expiry_Date
                FROM BATCH b
                JOIN ITEM i ON b.Item_ID = i.Item_ID
                ORDER BY b.Batch_ID DESC
            ")->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Sale failed: " . $e->getMessage();
        }
    }
}

// Fetch recent sales
$recent_sales = $pdo->query("
    SELECT s.Sale_ID, b.Batch_ID, i.Item_Name, s.Quantity_Sold, s.Sale_Date 
    FROM SALE s 
    JOIN BATCH b ON s.Batch_ID = b.Batch_ID 
    JOIN ITEM i ON b.Item_ID = i.Item_ID 
    ORDER BY s.Sale_Date DESC 
    LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<h2 class="page-title mb-3">Make Sale</h2>
<p class="text-muted">Sell quantity from a selected batch.</p>

<div class="card p-4 form-card">
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Batch</label>
            <select name="batch_id" class="form-select" required>
                <option value="">Select batch</option>
                <?php foreach ($batches as $batch): ?>
                    <option value="<?php echo $batch['Batch_ID']; ?>">
                        Batch #<?php echo $batch['Batch_ID']; ?> -
                        <?php echo htmlspecialchars($batch['Item_Name']); ?>
                        (Qty: <?php echo $batch['Quantity']; ?>, Exp: <?php echo $batch['Expiry_Date']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Sale Date</label>
            <input type="date" name="sale_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity Sold</label>
            <input type="number" name="quantity_sold" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-success">Save Sale</button>
    </form>
</div>

<div class="card p-4 mt-4 form-card">
    <h5 class="mb-3">Recent Sales</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Sale ID</th>
                    <th>Batch ID</th>
                    <th>Item Name</th>
                    <th>Quantity Sold</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recent_sales) > 0): ?>
                    <?php foreach ($recent_sales as $sale): ?>
                        <tr>
                            <td><?php echo $sale['Sale_ID']; ?></td>
                            <td><?php echo $sale['Batch_ID']; ?></td>
                            <td><?php echo htmlspecialchars($sale['Item_Name']); ?></td>
                            <td><?php echo $sale['Quantity_Sold']; ?></td>
                            <td><?php echo $sale['Sale_Date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No sales recorded.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>