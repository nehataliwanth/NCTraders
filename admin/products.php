<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = intval($_POST['product_id'] ?? 0);

    if ($productId > 0) {
        if ($action === 'approve') {
            update('products', ['status' => 'active'], ['id' => $productId]);
            $success = 'Product approved.';
        }
        if ($action === 'deactivate') {
            update('products', ['status' => 'inactive'], ['id' => $productId]);
            $success = 'Product deactivated.';
        }
        if ($action === 'delete_product') {
            delete('products', ['id' => $productId]);
            $success = 'Product deleted.';
        }
    }
}

$products = getAllRows('SELECT p.*, u.username AS seller_name, c.category_name FROM products p JOIN users u ON p.seller_id = u.id JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products - NC Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-crown"></i> Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <h1 class="fw-bold mb-4"><i class="fas fa-box-open"></i> Product Management</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Seller</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['seller_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td><?php echo formatCurrency($product['price']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $product['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <?php if ($product['status'] !== 'active'): ?>
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    <?php else: ?>
                                        <input type="hidden" name="action" value="deactivate">
                                        <button type="submit" class="btn btn-sm btn-warning">Deactivate</button>
                                    <?php endif; ?>
                                </form>
                                <form method="POST" class="d-inline ms-1" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="action" value="delete_product">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 NC Traders. All rights reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
