<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

requireSeller();
$currentUser = getCurrentUser();
$categories = getAllCategories();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = intval($_POST['product_id'] ?? 0);

    if ($action === 'delete' && $productId > 0) {
        delete('products', ['id' => $productId, 'seller_id' => $currentUser['id']]);
        $success = 'Product deleted successfully.';
    }

    if ($action === 'update' && $productId > 0) {
        $productName = sanitize($_POST['product_name'] ?? '');
        $categoryId = intval($_POST['category_id'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = sanitize($_POST['description'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');

        if (empty($productName) || $categoryId <= 0 || $price <= 0) {
            $error = 'Please provide valid product details.';
        } else {
            $updateData = [
                'product_name' => $productName,
                'category_id' => $categoryId,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = uploadFile($_FILES['image']);
                if ($uploadResult['success']) {
                    $updateData['image_url'] = $uploadResult['file_path'];
                } else {
                    $error = $uploadResult['message'];
                }
            }

            if (!$error) {
                update('products', $updateData, ['id' => $productId, 'seller_id' => $currentUser['id']]);
                $success = 'Product updated successfully.';
            }
        }
    }
}

$sellerProducts = getSellerProducts($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - NC Traders</title>
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
                    <li class="nav-item"><a class="nav-link active" href="manage-products.php">Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="add-product.php">Add Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold"><i class="fas fa-stream"></i> Manage Products</h1>
            <a href="add-product.php" class="btn btn-success"><i class="fas fa-plus"></i> Add New Product</a>
        </div>

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

        <?php if (empty($sellerProducts)): ?>
            <div class="alert alert-info">
                <p class="mb-0">You have no products yet. Add one to start selling.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sellerProducts as $product):
                            $category = getRow("SELECT category_name FROM categories WHERE id = " . intval($product['category_id']));
                        ?>
                            <tr>
                                <td style="width: 90px;">
                                    <?php if ($product['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="img-fluid rounded" style="max-height: 60px;">
                                    <?php else: ?>
                                        <div class="border rounded d-flex align-items-center justify-content-center" style="width: 90px; height: 60px; background:#f8f9fa;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($category['category_name'] ?? 'Uncategorized'); ?></td>
                                <td><?php echo formatCurrency($product['price']); ?></td>
                                <td><?php echo intval($product['stock']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : ($product['status'] === 'out_of_stock' ? 'danger' : 'secondary'); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $product['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-<?php echo $product['id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="collapse" id="edit-<?php echo $product['id']; ?>">
                                <td colspan="7">
                                    <div class="card card-body bg-light">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Product Name</label>
                                                    <input type="text" class="form-control" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Category</label>
                                                    <select class="form-select" name="category_id" required>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($cat['category_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Price (R)</label>
                                                    <input type="number" class="form-control" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Stock</label>
                                                    <input type="number" class="form-control" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status">
                                                        <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                        <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                        <option value="out_of_stock" <?php echo $product['status'] === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Image (optional)</label>
                                                    <input type="file" class="form-control" name="image" accept="image/*">
                                                </div>
                                                <div class="col-md-6 d-flex align-items-end justify-content-end">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-save"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 NC Traders. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
