<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

requireLogin();

$currentUser = getCurrentUser();
$cartItems = getCartItems($currentUser['id']);
$cartTotal = getCartTotal($currentUser['id']);

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['quantity'] * $item['price']);
}
$tax = $subtotal * 0.1;
$shipping = $subtotal > 100 ? 0 : 10;
$total = $subtotal + $tax + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - NC Traders</title>
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
                    <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <h1 class="mb-4 fw-bold">
            <i class="fas fa-shopping-cart"></i> Shopping Cart
        </h1>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <?php if (empty($cartItems)): ?>
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4>Your cart is empty</h4>
                        <p class="text-muted">Start shopping to add items to your cart</p>
                        <a href="product.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): 
                                        $itemTotal = $item['quantity'] * $item['price'];
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if ($item['image_url']): ?>
                                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                    <?php else: ?>
                                                        <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo formatCurrency($item['price']); ?></td>
                                            <td>
                                                <div class="input-group" style="width: 100px;">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                            onclick="updateQuantity(<?php echo $item['product_id']; ?>, Math.max(1, <?php echo $item['quantity']; ?> - 1))">
                                                        -
                                                    </button>
                                                    <input type="text" class="form-control text-center" value="<?php echo $item['quantity']; ?>" disabled>
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['quantity']; ?> + 1)">
                                                        +
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="fw-bold"><?php echo formatCurrency($itemTotal); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="if(confirm('Remove this item?')) removeFromCart(<?php echo $item['product_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light text-end">
                            <a href="product.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cart Summary -->
            <?php if (!empty($cartItems)): ?>
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-top">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span class="subtotal-value"><?php echo formatCurrency($subtotal); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tax (10%):</span>
                                <span class="tax-value"><?php echo formatCurrency($tax); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span>Shipping:</span>
                                <span class="shipping-value">
                                    <?php echo $shipping === 0 ? 'FREE' : formatCurrency($shipping); ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                                <span>Total:</span>
                                <span class="text-primary total-value"><?php echo formatCurrency($total); ?></span>
                            </div>

                            <a href="checkout.php" class="btn btn-primary w-100 btn-lg fw-bold mb-2">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                            <a href="product.php" class="btn btn-outline-secondary w-100">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 NC Traders. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        function updateQuantity(productId, newQuantity) {
            if (newQuantity < 1) return;
            // This would need an API endpoint to update the cart
            location.reload(); // For now, refresh the page
        }
    </script>
</body>
</html>
