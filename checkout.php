<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

requireLogin();

$currentUser = getCurrentUser();
$cartItems = getCartItems($currentUser['id']);

if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$error = '';
$success = '';

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['quantity'] * $item['price']);
}
$tax = $subtotal * 0.1;
$shipping = $subtotal > 100 ? 0 : 10;
$total = $subtotal + $tax + $shipping;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
    $billingAddress = sanitize($_POST['billing_address'] ?? '');
    $shippingCity = sanitize($_POST['shipping_city'] ?? '');
    $shippingState = sanitize($_POST['shipping_state'] ?? '');
    $shippingPostalCode = sanitize($_POST['shipping_postal_code'] ?? '');
    $shippingCountry = sanitize($_POST['shipping_country'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? '');

    // Validation
    if (empty($shippingAddress) || empty($shippingCity) || empty($shippingState) || empty($paymentMethod)) {
        $error = 'All shipping and payment fields are required';
    } else {
        // Create order
        $orderNumber = generateOrderNumber();
        $orderData = [
            'order_number' => $orderNumber,
            'user_id' => $currentUser['id'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_cost' => $shipping,
            'total_amount' => $total,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress ?: $shippingAddress,
            'shipping_city' => $shippingCity,
            'shipping_state' => $shippingState,
            'shipping_postal_code' => $shippingPostalCode,
            'shipping_country' => $shippingCountry,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
            'payment_status' => 'pending'
        ];

        $orderId = insert('orders', $orderData);

        if ($orderId) {
            // Add order items
            foreach ($cartItems as $item) {
                $itemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'seller_id' => $item['seller_id'] ?? 0,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price']
                ];
                insert('order_items', $itemData);

                // Clear cart
                delete('cart', ['user_id' => $currentUser['id'], 'product_id' => $item['product_id']]);
            }

            // Redirect to success
            $_SESSION['last_order'] = $orderNumber;
            header('Location: order-success.php?order=' . urlencode($orderNumber));
            exit;
        } else {
            $error = 'Failed to create order. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - NC Traders</title>
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
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <h1 class="mb-4 fw-bold">
            <i class="fas fa-credit-card"></i> Checkout
        </h1>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="checkout-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($currentUser['first_name'] ?? ''); ?>" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($currentUser['last_name'] ?? ''); ?>" disabled>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Street Address *</label>
                                <input type="text" class="form-control" name="shipping_address" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City *</label>
                                    <input type="text" class="form-control" name="shipping_city" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State/Province *</label>
                                    <input type="text" class="form-control" name="shipping_state" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" name="shipping_postal_code">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" class="form-control" name="shipping_country" value="South Africa">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Billing Address</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="sameAsShipping" checked onchange="toggleBillingAddress()">
                            <label class="form-check-label" for="sameAsShipping">
                                Same as shipping address
                            </label>
                        </div>
                        <div id="billingAddressForm" style="display: none;">
                            <input type="text" class="form-control" name="billing_address" placeholder="Billing Address">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="credit_card" required>
                            <label class="form-check-label" for="creditCard">
                                <i class="fas fa-credit-card"></i> Credit Card
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="debitCard" value="debit_card">
                            <label class="form-check-label" for="debitCard">
                                <i class="fas fa-credit-card"></i> Debit Card
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="bankTransfer" value="bank_transfer">
                            <label class="form-check-label" for="bankTransfer">
                                <i class="fas fa-building"></i> Bank Transfer
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <h6 class="fw-bold mb-2">Items:</h6>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between small mb-2">
                                    <span><?php echo htmlspecialchars($item['product_name']); ?> x<?php echo $item['quantity']; ?></span>
                                    <span><?php echo formatCurrency($item['quantity'] * $item['price']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span><?php echo formatCurrency($subtotal); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax (10%):</span>
                            <span><?php echo formatCurrency($tax); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span>Shipping:</span>
                            <span><?php echo $shipping === 0 ? 'FREE' : formatCurrency($shipping); ?></span>
                        </div>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span>Total:</span>
                            <span class="text-primary"><?php echo formatCurrency($total); ?></span>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-primary w-100 btn-lg fw-bold">
                            <i class="fas fa-check"></i> Complete Order
                        </button>
                        <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
                            Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 NC Traders. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleBillingAddress() {
            const checkbox = document.getElementById('sameAsShipping');
            const billingForm = document.getElementById('billingAddressForm');
            billingForm.style.display = checkbox.checked ? 'none' : 'block';
        }
    </script>
</body>
</html>
