<?php
include '../config/db.php';

$message = "";
$error = "";

$suppliers = $pdo->query("SELECT Supplier_ID, Supplier_Name FROM SUPPLIER ORDER BY Supplier_Name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? '';
    $purchase_date = $_POST['purchase_date'] ?? '';

    if ($supplier_id === '' || $purchase_date === '') {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO PURCHASE (Supplier_ID, Purchase_Date) VALUES (?, ?)");
            $stmt->execute([$supplier_id, $purchase_date]);
            $message = "Purchase added successfully.";
        } catch (PDOException $e) {
            $error = "Failed to add purchase: " . $e->getMessage();
        }
    }
}

// Fetch existing purchases
$existing_purchases = $pdo->query("
    SELECT p.Purchase_ID, s.Supplier_Name, p.Purchase_Date 
    FROM PURCHASE p 
    JOIN SUPPLIER s ON p.Supplier_ID = s.Supplier_ID 
    ORDER BY p.Purchase_Date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<h2 class="page-title mb-3">Add Purchase</h2>
<p class="text-muted">Create a purchase record from a supplier.</p>

<div class="card p-4 form-card">
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select" required>
                <option value="">Select supplier</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['Supplier_ID']; ?>">
                        <?php echo htmlspecialchars($supplier['Supplier_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Purchase Date</label>
            <input type="date" name="purchase_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Save Purchase</button>
    </form>
</div>

<div class="card p-4 mt-4 form-card">
    <h5 class="mb-3">Existing Purchases</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Purchase ID</th>
                    <th>Supplier Name</th>
                    <th>Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($existing_purchases) > 0): ?>
                    <?php foreach ($existing_purchases as $purchase): ?>
                        <tr>
                            <td><?php echo $purchase['Purchase_ID']; ?></td>
                            <td><?php echo htmlspecialchars($purchase['Supplier_Name']); ?></td>
                            <td><?php echo $purchase['Purchase_Date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center">No purchases recorded.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>