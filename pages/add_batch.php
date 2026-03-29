<?php
include '../config/db.php';

$message = "";
$error = "";

$items = $pdo->query("SELECT Item_ID, Item_Name FROM ITEM ORDER BY Item_Name")->fetchAll(PDO::FETCH_ASSOC);
$purchases = $pdo->query("
    SELECT p.Purchase_ID, p.Purchase_Date, s.Supplier_Name
    FROM PURCHASE p
    JOIN SUPPLIER s ON p.Supplier_ID = s.Supplier_ID
    ORDER BY p.Purchase_ID DESC
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'] ?? '';
    $purchase_id = $_POST['purchase_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $mfg_date = $_POST['manufacturing_date'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';

    if ($item_id === '' || $purchase_id === '' || $quantity === '' || $mfg_date === '' || $expiry_date === '') {
        $error = "All fields are required.";
    } elseif ($quantity <= 0) {
        $error = "Quantity must be a positive number.";
    } elseif ($mfg_date >= $expiry_date) {
        $error = "Manufacturing date must be before the expiry date.";
    } else {
        try {
            $checkStmt = $pdo->prepare("SELECT Batch_ID FROM BATCH WHERE Item_ID = ? AND Manufacturing_Date = ? AND Expiry_Date = ?");
            $checkStmt->execute([$item_id, $mfg_date, $expiry_date]);
            $existingBatch = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingBatch) {
                $updateStmt = $pdo->prepare("UPDATE BATCH SET Quantity = Quantity + ? WHERE Batch_ID = ?");
                $updateStmt->execute([$quantity, $existingBatch['Batch_ID']]);
                $message = "Existing batch updated! Added {$quantity} items to Batch #" . $existingBatch['Batch_ID'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO BATCH (Item_ID, Purchase_ID, Quantity, Manufacturing_Date, Expiry_Date) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$item_id, $purchase_id, $quantity, $mfg_date, $expiry_date]);
                $message = "New batch added successfully.";
            }
        } catch (PDOException $e) {
            $error = "Failed to save batch: " . $e->getMessage();
        }
    }
}

// Fetch existing batches
$existing_batches = $pdo->query("
    SELECT b.Batch_ID, i.Item_Name, b.Quantity, b.Manufacturing_Date, b.Expiry_Date 
    FROM BATCH b 
    JOIN ITEM i ON b.Item_ID = i.Item_ID 
    ORDER BY b.Batch_ID DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<h2 class="page-title mb-3">Add Batch</h2>
<p class="text-muted">Add stock batch with manufacturing and expiry details.</p>

<div class="card p-4 form-card">
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Item</label>
            <select name="item_id" class="form-select" required>
                <option value="">Select item</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?php echo $item['Item_ID']; ?>">
                        <?php echo htmlspecialchars($item['Item_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Purchase</label>
            <select name="purchase_id" class="form-select" required>
                <option value="">Select purchase</option>
                <?php foreach ($purchases as $purchase): ?>
                    <option value="<?php echo $purchase['Purchase_ID']; ?>">
                        Purchase #<?php echo $purchase['Purchase_ID']; ?> -
                        <?php echo htmlspecialchars($purchase['Supplier_Name']); ?> -
                        <?php echo $purchase['Purchase_Date']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Manufacturing Date</label>
            <input type="date" name="manufacturing_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Save Batch</button>
    </form>
</div>

<div class="card p-4 mt-4 form-card">
    <h5 class="mb-3">Existing Batches</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Batch ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Manufacturing Date</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($existing_batches) > 0): ?>
                    <?php foreach ($existing_batches as $batch): ?>
                        <tr>
                            <td><?php echo $batch['Batch_ID']; ?></td>
                            <td><?php echo htmlspecialchars($batch['Item_Name']); ?></td>
                            <td><?php echo $batch['Quantity']; ?></td>
                            <td><?php echo $batch['Manufacturing_Date']; ?></td>
                            <td><?php echo $batch['Expiry_Date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No batches recorded.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const mfgInput = document.getElementsByName('manufacturing_date')[0];
    const expInput = document.getElementsByName('expiry_date')[0];

    mfgInput.addEventListener('change', function() {
        if (this.value) {
            // Set the minimum expiry date to one day after the manufacturing date
            let mfgStr = this.value;
            let mfgDate = new Date(mfgStr);
            mfgDate.setDate(mfgDate.getDate() + 1);
            expInput.min = mfgDate.toISOString().split('T')[0];
        } else {
            expInput.min = "";
        }
    });
});
</script>

<?php include '../partials/footer.php'; ?>