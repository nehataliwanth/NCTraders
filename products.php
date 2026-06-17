<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';
include 'config/database.php';
$currentUser = getCurrentUser();
$sql = "SELECT products.*, users.username AS seller_name
FROM products
JOIN users
ON products.seller_id = users.id
ORDER BY products.id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products | NC Traders</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="container py-5" style="margin-top:120px;">
<button onclick="history.back()"
class="btn btn-dark rounded-pill mb-4">
<i class="fas fa-arrow-left"></i>
Back
</button>
<h1 class="fw-bold mb-5">
All Products
</h1>
<div class="row g-4">
<?php while($row = mysqli_fetch_assoc($result)): ?>
<?php
$productImage = $row['image_url'] ?? '';
$productPrice = $row['price'] ?? 0;
$productDescription = $row['product_description'] ?? '';
$productStatus =
$row['product_status']
??
$row['status']
??
'Available';
?>
<div class="col-md-6 col-lg-3">
<div class="product-card">
<div class="product-image-wrapper">
<img src="<?php echo htmlspecialchars(
strpos($productImage,'http') === 0
? $productImage
: 'uploads/products/' . $productImage
); ?>"
class="product-image">
<div class="product-status <?php echo strtolower(trim($productStatus)) == 'sold out' ? 'status-danger' : 'status-success'; ?>">
<?php echo strtolower(trim($productStatus)) == 'sold out' ? 'SOLD OUT' : 'AVAILABLE'; ?>
</div>
</div>
<div class="product-body">
<h5>
<?php echo htmlspecialchars($row['product_name']); ?>
</h5>
<div class="seller-name">
Seller: <?php echo htmlspecialchars($row['seller_name']); ?>
</div>
<div class="product-price">
R<?php echo number_format($productPrice,2); ?>
</div>
<p>
<?php echo htmlspecialchars(substr($productDescription,0,80)); ?>...
</p>
<a href="product.php?id=<?php echo $row['id']; ?>"
class="btn btn-dark w-100 rounded-pill mt-3">
View Product
</a>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
