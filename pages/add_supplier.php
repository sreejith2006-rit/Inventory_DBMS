<?php
include '../config/db.php';

// --- AJAX Endpoint for Auto-fetch ---
if (isset($_GET['action']) && $_GET['action'] === 'get_supplier') {
    $name = trim($_GET['name'] ?? '');
    if ($name !== '') {
        $stmt = $pdo->prepare("SELECT Email FROM SUPPLIER WHERE Supplier_Name = ?");
        $stmt->execute([$name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            echo json_encode(['found' => true, 'email' => $row['Email']]);
        } else {
            echo json_encode(['found' => false]);
        }
    } else {
        echo json_encode(['found' => false]);
    }
    exit;
}
// ------------------------------------

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($supplier_name === '' || $phone === '') {
        $error = "Supplier name and phone number are required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Please enter a valid phone number containing only 10 digits.";
    } else {
        try {
            $pdo->beginTransaction();

            // Step 1: Check if supplier already exists by name
            $checkStmt = $pdo->prepare("SELECT Supplier_ID, Email FROM SUPPLIER WHERE Supplier_Name = ?");
            $checkStmt->execute([$supplier_name]);
            $existingSupplier = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingSupplier) {
                // Supplier already exists, reuse same Supplier_ID
                $supplier_id = $existingSupplier['Supplier_ID'];

                // Optional: if email field is given and existing email is empty, update it
                if ($email !== '' && empty($existingSupplier['Email'])) {
                    $updateEmailStmt = $pdo->prepare("UPDATE SUPPLIER SET Email = ? WHERE Supplier_ID = ?");
                    $updateEmailStmt->execute([$email, $supplier_id]);
                }
            } else {
                // Supplier does not exist, insert new supplier
                $insertSupplierStmt = $pdo->prepare("INSERT INTO SUPPLIER (Supplier_Name, Email) VALUES (?, ?)");
                $insertSupplierStmt->execute([$supplier_name, $email !== '' ? $email : null]);

                $supplier_id = $pdo->lastInsertId();
            }

            // Step 2: Check if this phone number already exists for that supplier
            $phoneCheckStmt = $pdo->prepare("SELECT * FROM SUPPLIER_PHONE WHERE Supplier_ID = ? AND Phone_No = ?");
            $phoneCheckStmt->execute([$supplier_id, $phone]);

            if ($phoneCheckStmt->fetch()) {
                $pdo->rollBack();
                $error = "This phone number already exists for the supplier.";
            } else {
                // Step 3: Insert the new phone number
                $insertPhoneStmt = $pdo->prepare("INSERT INTO SUPPLIER_PHONE (Supplier_ID, Phone_No) VALUES (?, ?)");
                $insertPhoneStmt->execute([$supplier_id, $phone]);

                $pdo->commit();

                if ($existingSupplier) {
                    $message = "New phone number added to existing supplier.";
                } else {
                    $message = "Supplier added successfully.";
                }
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Operation failed: " . $e->getMessage();
        }
    }
}

// Fetch existing suppliers
$existing_suppliers = $pdo->query("
    SELECT s.Supplier_ID, s.Supplier_Name, s.Email, GROUP_CONCAT(p.Phone_No SEPARATOR ', ') as Phones 
    FROM SUPPLIER s 
    LEFT JOIN SUPPLIER_PHONE p ON s.Supplier_ID = p.Supplier_ID 
    GROUP BY s.Supplier_ID
    ORDER BY s.Supplier_Name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<h2 class="page-title mb-3">Add Supplier</h2>
<p class="text-muted">Add a new supplier or add another phone number to an existing supplier.</p>

<div class="card p-4 form-card">
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Supplier Name</label>
            <input type="text" name="supplier_name" id="supplier_name" class="form-control" required autocomplete="off">
        </div>

        <div class="mb-3">
            <label class="form-label">Email <span id="email_locked_notice" class="badge bg-secondary ms-2"
                    style="display:none;">Sealed (Existing)</span></label>
            <input type="email" name="email" id="email" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control" pattern="[0-9]{10}"
                title="Please enter a valid 10 digit phone number" maxlength="10" required>
        </div>

        <button type="submit" class="btn btn-success">Save Supplier / Add Phone</button>
    </form>
</div>

<script>
    document.getElementById('supplier_name').addEventListener('blur', function () {
        const name = this.value.trim();
        const emailInput = document.getElementById('email');
        const notice = document.getElementById('email_locked_notice');

        if (name === '') {
            emailInput.value = '';
            emailInput.readOnly = false;
            notice.style.display = 'none';
            return;
        }

        fetch('add_supplier.php?action=get_supplier&name=' + encodeURIComponent(name))
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    // Supplier exists, seal the email field
                    emailInput.value = data.email || '';
                    emailInput.readOnly = true;
                    notice.style.display = 'inline-block';
                } else {
                    // New supplier, leave field open
                    emailInput.value = '';
                    emailInput.readOnly = false;
                    notice.style.display = 'none';
                }
            })
            .catch(err => console.error(err));
    });
</script>

<div class="card p-4 mt-4 form-card">
    <h5 class="mb-3">Existing Suppliers</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Name</th>
                    <th>Email</th>
                    <th>Phone Numbers</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($existing_suppliers) > 0): ?>
                    <?php foreach ($existing_suppliers as $sup): ?>
                        <tr>
                            <td><?php echo $sup['Supplier_ID']; ?></td>
                            <td><?php echo htmlspecialchars($sup['Supplier_Name']); ?></td>
                            <td><?php echo htmlspecialchars($sup['Email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($sup['Phones'] ?? 'None'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No suppliers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>