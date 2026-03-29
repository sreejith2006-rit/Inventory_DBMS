<?php
include 'config/db.php';

$total_items = $pdo->query("SELECT COUNT(*) FROM ITEM")->fetchColumn();
$total_suppliers = $pdo->query("SELECT COUNT(*) FROM SUPPLIER")->fetchColumn();
$total_batches = $pdo->query("SELECT COUNT(*) FROM BATCH")->fetchColumn();
$total_sales = $pdo->query("SELECT COUNT(*) FROM SALE")->fetchColumn();

$near_expiry = $pdo->query("
    SELECT COUNT(*)
    FROM BATCH
    WHERE Expiry_Date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
")->fetchColumn();

$low_stock = $pdo->query("
    SELECT COUNT(*)
    FROM (
        SELECT i.Item_ID
        FROM ITEM i
        LEFT JOIN BATCH b ON i.Item_ID = b.Item_ID
        GROUP BY i.Item_ID, i.Min_Stock
        HAVING COALESCE(SUM(b.Quantity), 0) < i.Min_Stock
    ) AS low_items
")->fetchColumn();

// Fetch data for charts
$stock_data = $pdo->query("
    SELECT i.Item_Name, COALESCE(SUM(b.Quantity), 0) AS Total_Stock
    FROM ITEM i
    LEFT JOIN BATCH b ON i.Item_ID = b.Item_ID
    GROUP BY i.Item_ID, i.Item_Name
    ORDER BY Total_Stock DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$item_names = json_encode(array_column($stock_data, 'Item_Name'));
$item_stocks = json_encode(array_column($stock_data, 'Total_Stock'));

$expiry_stats = $pdo->query("
    SELECT 
        SUM(CASE WHEN Expiry_Date > DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as Safe,
        SUM(CASE WHEN Expiry_Date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as Near_Expiry,
        SUM(CASE WHEN Expiry_Date < CURDATE() THEN 1 ELSE 0 END) as Expired
    FROM BATCH
")->fetch(PDO::FETCH_ASSOC);

$expiry_labels = json_encode(['Safe Stock', 'Near Expiry', 'Expired']);
$expiry_values = json_encode([(int)$expiry_stats['Safe'], (int)$expiry_stats['Near_Expiry'], (int)$expiry_stats['Expired']]);
?>

<?php include 'partials/header.php'; ?>
<?php include 'partials/sidebar.php'; ?>

<h2 class="page-title mb-3">Dashboard</h2>
<p class="text-muted">Welcome to the Inventory & Expiry Tracking System.</p>

<div class="row g-4 mt-2 mb-4">
    <div class="col-md-2 col-sm-4">
        <div class="card p-3">
            <h5>Total Items</h5>
            <p class="fs-4 mb-0"><?php echo $total_items; ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3">
            <h5>Suppliers</h5>
            <p class="fs-4 mb-0"><?php echo $total_suppliers; ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3">
            <h5>Batches</h5>
            <p class="fs-4 mb-0"><?php echo $total_batches; ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3">
            <h5>Sales</h5>
            <p class="fs-4 mb-0"><?php echo $total_sales; ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3">
            <h5>Near Expiry</h5>
            <p class="fs-4 mb-0 text-warning"><?php echo $near_expiry; ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-4">
        <div class="card p-3">
            <h5>Low Stock</h5>
            <p class="fs-4 mb-0 text-danger"><?php echo $low_stock; ?></p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card p-4 h-100">
            <h5>Top Stock Items (Inventory Levels)</h5>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <h5>Batch Expiry Status</h5>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="expiryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Config for Stock Bar Chart
    const stockCtx = document.getElementById('stockChart').getContext('2d');
    new Chart(stockCtx, {
        type: 'bar',
        data: {
            labels: <?php echo $item_names; ?>,
            datasets: [{
                label: 'Total Quantity in Stock',
                data: <?php echo $item_stocks; ?>,
                backgroundColor: 'rgba(56, 189, 248, 0.7)',
                borderColor: 'rgba(2, 132, 199, 1)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Config for Expiry Doughnut Chart
    const expiryCtx = document.getElementById('expiryChart').getContext('2d');
    new Chart(expiryCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo $expiry_labels; ?>,
            datasets: [{
                data: <?php echo $expiry_values; ?>,
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',   // Safe (Green)
                    'rgba(245, 158, 11, 0.8)',  // Near Expiry (Yellow)
                    'rgba(239, 68, 68, 0.8)'    // Expired (Red)
                ],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

<?php include 'partials/footer.php'; ?>