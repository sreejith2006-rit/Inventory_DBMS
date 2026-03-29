<?php
include '../config/db.php';


/* 1. Current stock per item */
$current_stock = $pdo->query("
    SELECT 
        i.Item_ID,
        i.Item_Name,
        c.Category_Name,
        COALESCE(SUM(b.Quantity), 0) AS Total_Stock,
        i.Min_Stock
    FROM ITEM i
    JOIN CATEGORY c ON i.Category_ID = c.Category_ID
    LEFT JOIN BATCH b ON i.Item_ID = b.Item_ID
    GROUP BY i.Item_ID, i.Item_Name, c.Category_Name, i.Min_Stock
    ORDER BY i.Item_Name
")->fetchAll(PDO::FETCH_ASSOC);

/* 2. Near expiry batches: next 7 days */
$near_expiry_batches = $pdo->query("
    SELECT 
        b.Batch_ID,
        i.Item_Name,
        b.Quantity,
        b.Manufacturing_Date,
        b.Expiry_Date
    FROM BATCH b
    JOIN ITEM i ON b.Item_ID = i.Item_ID
    WHERE b.Expiry_Date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY b.Expiry_Date
")->fetchAll(PDO::FETCH_ASSOC);

/* 3. Expired batches */
$expired_batches = $pdo->query("
    SELECT 
        b.Batch_ID,
        i.Item_Name,
        b.Quantity,
        b.Expiry_Date
    FROM BATCH b
    JOIN ITEM i ON b.Item_ID = i.Item_ID
    WHERE b.Expiry_Date < CURDATE()
    ORDER BY b.Expiry_Date
")->fetchAll(PDO::FETCH_ASSOC);

/* 4. Low stock items */
$low_stock_items = $pdo->query("
    SELECT 
        i.Item_ID,
        i.Item_Name,
        COALESCE(SUM(b.Quantity), 0) AS Total_Stock,
        i.Min_Stock
    FROM ITEM i
    LEFT JOIN BATCH b ON i.Item_ID = b.Item_ID
    GROUP BY i.Item_ID, i.Item_Name, i.Min_Stock
    HAVING COALESCE(SUM(b.Quantity), 0) < i.Min_Stock
    ORDER BY i.Item_Name
")->fetchAll(PDO::FETCH_ASSOC);

/* 5. Supplier-wise purchase summary */
$supplier_purchase_summary = $pdo->query("
    SELECT 
        s.Supplier_ID,
        s.Supplier_Name,
        COUNT(p.Purchase_ID) AS Total_Purchases
    FROM SUPPLIER s
    LEFT JOIN PURCHASE p ON s.Supplier_ID = p.Supplier_ID
    GROUP BY s.Supplier_ID, s.Supplier_Name
    ORDER BY s.Supplier_Name
")->fetchAll(PDO::FETCH_ASSOC);
/* 6. All Batches */
$all_batches = $pdo->query("
    SELECT 
        b.Batch_ID,
        i.Item_Name,
        p.Purchase_ID,
        b.Quantity,
        b.Manufacturing_Date,
        b.Expiry_Date
    FROM BATCH b
    JOIN ITEM i ON b.Item_ID = i.Item_ID
    LEFT JOIN PURCHASE p ON b.Purchase_ID = p.Purchase_ID
    ORDER BY b.Batch_ID DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* 7. All Purchases */
$all_purchases = $pdo->query("
    SELECT 
        p.Purchase_ID,
        s.Supplier_Name,
        p.Purchase_Date
    FROM PURCHASE p
    LEFT JOIN SUPPLIER s ON p.Supplier_ID = s.Supplier_ID
    ORDER BY p.Purchase_Date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="page-title mb-0">Reports</h2>
        <p class="text-muted mb-0">View stock, expiry, and supplier-wise summary reports.</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary d-print-none shadow-sm">
        <i class="bi bi-printer me-2"></i>Print Report
    </button>
</div>


<div class="card p-4 mb-4">
    <h5 class="mb-3">Current Stock Report</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Total Stock</th>
                    <th>Min Stock</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($current_stock as $row): ?>
                    <tr>
                        <td><?php echo $row['Item_ID']; ?></td>
                        <td><?php echo htmlspecialchars($row['Item_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Category_Name']); ?></td>
                        <td><?php echo $row['Total_Stock']; ?></td>
                        <td><?php echo $row['Min_Stock']; ?></td>
                        <td>
                            <?php echo ($row['Total_Stock'] < $row['Min_Stock']) ? 'Low Stock' : 'Normal'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card p-4 mb-4">
    <h5 class="mb-3">Near Expiry Batches (Next 7 Days)</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-warning">
                <tr>
                    <th>Batch ID</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Manufacturing Date</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($near_expiry_batches) > 0): ?>
                    <?php foreach ($near_expiry_batches as $batch): ?>
                        <tr>
                            <td><?php echo $batch['Batch_ID']; ?></td>
                            <td><?php echo htmlspecialchars($batch['Item_Name']); ?></td>
                            <td><?php echo $batch['Quantity']; ?></td>
                            <td><?php echo $batch['Manufacturing_Date']; ?></td>
                            <td><?php echo $batch['Expiry_Date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No near expiry batches found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card p-4 mb-4">
    <h5 class="mb-3">Expired Batches</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-danger">
                <tr>
                    <th>Batch ID</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($expired_batches) > 0): ?>
                    <?php foreach ($expired_batches as $batch): ?>
                        <tr>
                            <td><?php echo $batch['Batch_ID']; ?></td>
                            <td><?php echo htmlspecialchars($batch['Item_Name']); ?></td>
                            <td><?php echo $batch['Quantity']; ?></td>
                            <td><?php echo $batch['Expiry_Date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No expired batches found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card p-4 mb-4">
    <h5 class="mb-3">Low Stock Items</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-info">
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Total Stock</th>
                    <th>Min Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($low_stock_items) > 0): ?>
                    <?php foreach ($low_stock_items as $item): ?>
                        <tr>
                            <td><?php echo $item['Item_ID']; ?></td>
                            <td><?php echo htmlspecialchars($item['Item_Name']); ?></td>
                            <td><?php echo $item['Total_Stock']; ?></td>
                            <td><?php echo $item['Min_Stock']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No low stock items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card p-4 mb-4">
    <h5 class="mb-3">Supplier-wise Purchase Summary</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-secondary">
                <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Name</th>
                    <th>Total Purchases</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($supplier_purchase_summary as $supplier): ?>
                    <tr>
                        <td><?php echo $supplier['Supplier_ID']; ?></td>
                        <td><?php echo htmlspecialchars($supplier['Supplier_Name']); ?></td>
                        <td><?php echo $supplier['Total_Purchases']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card p-4 mb-4">
    <h5 class="mb-3">All Purchases</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Purchase ID</th>
                    <th>Supplier Name</th>
                    <th>Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($all_purchases) > 0): ?>
                    <?php foreach ($all_purchases as $purchase): ?>
                        <tr>
                            <td><?php echo $purchase['Purchase_ID']; ?></td>
                            <td><?php echo htmlspecialchars($purchase['Supplier_Name'] ?? 'Unknown'); ?></td>
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

<div class="card p-4">
    <h5 class="mb-3">All Batches</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Batch ID</th>
                    <th>Item</th>
                    <th>Purchase ID</th>
                    <th>Quantity</th>
                    <th>Manufacturing Date</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($all_batches) > 0): ?>
                    <?php foreach ($all_batches as $batch): ?>
                        <tr>
                            <td><?php echo $batch['Batch_ID']; ?></td>
                            <td><?php echo htmlspecialchars($batch['Item_Name']); ?></td>
                            <td><?php echo $batch['Purchase_ID']; ?></td>
                            <td><?php echo $batch['Quantity']; ?></td>
                            <td><?php echo $batch['Manufacturing_Date']; ?></td>
                            <td><?php echo $batch['Expiry_Date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No batches recorded.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>