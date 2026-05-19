<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin();

$userCount = getRow('SELECT COUNT(*) AS total FROM users')['total'] ?? 0;
$productCount = getRow('SELECT COUNT(*) AS total FROM products')['total'] ?? 0;
$orderCount = getRow('SELECT COUNT(*) AS total FROM orders')['total'] ?? 0;
$sellerCount = getRow('SELECT COUNT(*) AS total FROM users WHERE role_id = 3')['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NC Traders</title>
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
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php">Site</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <h1 class="fw-bold mb-4"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-users text-primary fa-3x mb-3"></i>
                        <h6 class="text-muted mb-2">Total Users</h6>
                        <h2 class="fw-bold"><?php echo $userCount; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-store text-success fa-3x mb-3"></i>
                        <h6 class="text-muted mb-2">Products</h6>
                        <h2 class="fw-bold"><?php echo $productCount; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-bag text-warning fa-3x mb-3"></i>
                        <h6 class="text-muted mb-2">Orders</h6>
                        <h2 class="fw-bold"><?php echo $orderCount; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-user-tie text-danger fa-3x mb-3"></i>
                        <h6 class="text-muted mb-2">Sellers</h6>
                        <h2 class="fw-bold"><?php echo $sellerCount; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="users.php" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-user-cog"></i> Manage Users
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="products.php" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-box-open"></i> Manage Products
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="../index.php" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-home"></i> View Marketplace
                        </a>
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
