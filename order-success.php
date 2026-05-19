<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

requireLogin();
$currentUser = getCurrentUser();
$orderNumber = sanitize($_GET['order'] ?? $_SESSION['last_order'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - NC Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-store"></i> NC Traders
            </a>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                        <h1 class="fw-bold mb-3">Thank you for your order!</h1>
                        <p class="lead mb-4">Your order has been placed successfully.</p>
                        <?php if ($orderNumber): ?>
                            <p class="mb-4">Order Number: <strong><?php echo htmlspecialchars($orderNumber); ?></strong></p>
                        <?php endif; ?>
                        <a href="index.php" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <a href="product.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
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
