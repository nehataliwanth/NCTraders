<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

requireSeller();

$currentUser = getCurrentUser();
$sellerProducts = getSellerProducts($currentUser['id']);
$sellerOrders = getSellerOrders($currentUser['id']);

// Calculate stats
$totalProducts = count($sellerProducts);
$totalOrders = count($sellerOrders);
$totalRevenue = 0;
foreach ($sellerOrders as $order) {
    $totalRevenue += $order['total_amount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - NC Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-store"></i> NC Traders
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <h1 class="mb-4 fw-bold">
            <i class="fas fa-tachometer-alt"></i> Seller Dashboard
        </h1>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-boxes text-primary fa-3x mb-3"></i>
                        <h6 class="text-muted mb-2">Total Products</h6>
                        <h2 class="fw-bold text-primary"><?php echo $totalProducts; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-bag text-success fa-3x mb-3"></i>
                        <h6 class="text-muted mb-2">Total Orders</h6>
                        <h2 class="fw-bold text-success"><?php echo $totalOrders; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign text-warning fa-3x mb-3"></i>
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <h2 class="fw-bold text-warning"><?php echo formatCurrency($totalRevenue); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-star text-danger fa-3x mb-3"></i>
                        <h6 class="text-muted mb-2">Avg Rating</h6>
                        <h2 class="fw-bold text-danger">4.5★</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cube"></i> My Products
                </h5>
                <a href="add-product.php" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($sellerProducts)): ?>
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p>No products yet. Start by adding your first product!</p>
                        <a href="add-product.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Sales</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sellerProducts as $product): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                        </td>
                                        <td><?php echo formatCurrency($product['price']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $product['stock'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $product['stock']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $product['review_count'] ?? 0; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($product['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="#edit-product-<?php echo $product['id']; ?>" class="btn btn-primary" data-bs-toggle="modal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="#delete-product-<?php echo $product['id']; ?>" class="btn btn-danger" data-bs-toggle="modal">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-bag"></i> Recent Orders
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($sellerOrders)): ?>
                    <div class="alert alert-info text-center py-4">
                        <p>No orders yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($sellerOrders, 0, 10) as $order):
                                    $customer = getUserById($order['user_id']);
                                ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($customer['username'] ?? 'Unknown'); ?></td>
                                        <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $order['status'] === 'delivered' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($order['created_at']); ?></td>
                                        <td>
                                            <a href="#order-detail-<?php echo $order['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="modal">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 NC Traders. All rights reserved.</p>
        </div>
    </footer>

    <!-- Add Product Modal -->
    <div class="modal fade" id="add-product" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="product_name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <?php foreach (getAllCategories() as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price *</label>
                                <input type="number" class="form-control" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock *</label>
                                <input type="number" class="form-control" name="stock" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
