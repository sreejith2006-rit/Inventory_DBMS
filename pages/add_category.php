<?php
include '../config/db.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name'] ?? '');

    if ($category_name === '') {
        $error = "Category name is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO CATEGORY (Category_Name) VALUES (?)");
            $stmt->execute([$category_name]);
            $message = "Category added successfully.";
        } catch (PDOException $e) {
            $error = "Failed to add category: " . $e->getMessage();
        }
    }
}

// Fetch existing categories
$existing_categories = $pdo->query("SELECT * FROM CATEGORY ORDER BY Category_Name")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<h2 class="page-title mb-3">Add Category</h2>
<p class="text-muted">Create a new product category.</p>

<div class="card p-4 form-card">
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="category_name" class="form-control" placeholder="Enter category name" required>
        </div>
        <button type="submit" class="btn btn-success">Save Category</button>
    </form>
</div>

<div class="card p-4 mt-4 form-card">
    <h5 class="mb-3">Existing Categories</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($existing_categories) > 0): ?>
                    <?php foreach ($existing_categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['Category_ID']; ?></td>
                            <td><?php echo htmlspecialchars($cat['Category_Name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="text-center">No categories found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>