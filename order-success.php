<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';
include 'config/database.php';

requireLogin();
$currentUser = getCurrentUser();

$order_id = intval($_GET['order_id'] ?? 0);

if($order_id <= 0){
    header("Location: orders.php");
    exit();
}

// Fetch order details
$order_query = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id' AND user_id='{$currentUser['id']}'");

if(mysqli_num_rows($order_query) == 0){
    header("Location: orders.php");
    exit();
}

$order = mysqli_fetch_assoc($order_query);

// Fetch order items
$items_query = mysqli_query($conn, "SELECT order_items.*, products.product_name
                                    FROM order_items 
                                    JOIN products ON order_items.product_id = products.id 
                                    WHERE order_items.order_id='$order_id'");
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
    <style>
        .payment-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        .status-timeline {
            display: flex;
            justify-content: space-between;
            margin: 2rem 0;
        }
        .status-step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .status-step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
        }
        .status-step:last-child::after {
            display: none;
        }
        .status-step .badge {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 1.2rem;
            position: relative;
            z-index: 1;
        }
        .status-step.active .badge {
            background-color: #28a745;
        }
        .status-step.inactive .badge {
            background-color: #6c757d;
        }
    </style>
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
            <div class="col-lg-10">
                <!-- Success Header -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle" style="font-size: 5rem; color: #28a745;"></i>
                        </div>
                        <h1 class="fw-bold mb-3">Order Confirmed! 🎉</h1>
                        <p class="lead mb-2">Your payment has been authorized and your order is being processed.</p>
                        <p class="text-muted">A confirmation email has been sent to <?php echo htmlspecialchars($currentUser['email']); ?></p>
                    </div>
                </div>

                <!-- Order Information Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt"></i> Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Order Number:</strong></p>
                                <p class="h6 text-primary"><?php echo htmlspecialchars($order['order_number']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Tracking Number:</strong></p>
                                <p class="h6 text-success"><i class="fas fa-box"></i> <?php echo htmlspecialchars($order['tracking_number']); ?></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Order Date:</strong></p>
                                <p><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Payment Status:</strong></p>
                                <span class="badge bg-success payment-badge">
                                    <i class="fas fa-check-circle"></i> <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Payment Method:</strong></p>
                                <p><?php echo htmlspecialchars($order['payment_method']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Delivery Address:</strong></p>
                                <p><?php echo htmlspecialchars($order['shipping_address'] . ', ' . $order['shipping_city']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Status Timeline -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-route"></i> Order Tracking Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="status-timeline">
                            <div class="status-step <?php echo ($order['status'] != 'pending') ? 'active' : 'active'; ?>">
                                <div class="badge bg-success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p><strong>Confirmed</strong></p>
                                <small class="text-muted">Payment Authorized</small>
                            </div>

                            <div class="status-step <?php echo ($order['status'] == 'shipped') ? 'active' : 'inactive'; ?>">
                                <div class="badge <?php echo ($order['status'] == 'shipped' || $order['status'] == 'delivered') ? 'bg-success' : 'bg-secondary'; ?>">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                                <p><strong>Processing</strong></p>
                                <small class="text-muted">Preparing for shipment</small>
                            </div>

                            <div class="status-step <?php echo ($order['status'] == 'shipped') ? 'active' : 'inactive'; ?>">
                                <div class="badge <?php echo ($order['status'] == 'shipped' || $order['status'] == 'delivered') ? 'bg-success' : 'bg-secondary'; ?>">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <p><strong>Shipped</strong></p>
                                <small class="text-muted">On the way to you</small>
                            </div>

                            <div class="status-step <?php echo ($order['status'] == 'delivered') ? 'active' : 'inactive'; ?>">
                                <div class="badge <?php echo ($order['status'] == 'delivered') ? 'bg-success' : 'bg-secondary'; ?>">
                                    <i class="fas fa-home"></i>
                                </div>
                                <p><strong>Delivered</strong></p>
                                <small class="text-muted">Order complete</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($item = mysqli_fetch_assoc($items_query)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>R<?php echo number_format($item['unit_price'], 2); ?></td>
                                        <td><strong>R<?php echo number_format($item['total_price'], 2); ?></strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Total -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="mb-2 d-flex justify-content-between">
                                    <span>Subtotal:</span>
                                    <span>R<?php echo number_format($order['subtotal'], 2); ?></span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between">
                                    <span>Tax (15%):</span>
                                    <span>R<?php echo number_format($order['tax'], 2); ?></span>
                                </div>
                                <div class="mb-3 d-flex justify-content-between">
                                    <span>Shipping:</span>
                                    <span>R<?php echo number_format($order['shipping_cost'], 2); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-0"><strong>Total:</strong></h5>
                                    <h5 class="mb-0 text-success"><strong>R<?php echo number_format($order['total_amount'], 2); ?></strong></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-sm-flex justify-content-center mb-5">
                    <a href="orders.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-list"></i> View All Orders
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>

                <!-- Support Info -->
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Need help? Track your order using tracking number: <strong><?php echo htmlspecialchars($order['tracking_number']); ?></strong>
                    or contact support@nctraders.com
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
