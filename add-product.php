<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

requireSeller();
$currentUser = getCurrentUser();
$categories = getAllCategories();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = sanitize($_POST['product_name'] ?? '');
    $categoryId = intval($_POST['category_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');

    if (empty($productName) || $categoryId <= 0 || $price <= 0) {
        $error = 'Please complete all required product fields.';
    } else {
        $slug = generateSlug($productName);
        $existing = getRow("SELECT id FROM products WHERE slug = '" . escapeString($slug) . "'");
        if ($existing) {
            $slug .= '-' . time();
        }

        $imageUrl = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadFile($_FILES['image']);
            if ($uploadResult['success']) {
                $imageUrl = $uploadResult['file_path'];
            } else {
                $error = $uploadResult['message'];
            }
        }

        if (!$error) {
            $productData = [
                'seller_id' => $currentUser['id'],
                'category_id' => $categoryId,
                'product_name' => $productName,
                'slug' => $slug,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'image_url' => $imageUrl,
                'status' => $status
            ];

            $productId = insert('products', $productData);
            if ($productId) {
                $success = 'Product added successfully. You can manage it from your product dashboard.';
            } else {
                $error = 'Unable to save the product. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - NC Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-store"></i> NC Traders
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage-products.php">Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Add New Product</h4>
                    </div>
                    <div class="card-body">
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

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control" name="product_name" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category *</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Choose category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (R)</label>
                                    <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea class="form-control" name="description" rows="4"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" name="stock" min="0" value="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Save Product
                            </button>
                            <a href="manage-products.php" class="btn btn-secondary ms-2">Manage Products</a>
                        </form>
                    </div>
                </div>
            </div>
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
