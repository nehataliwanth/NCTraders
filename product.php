<?php
session_start();

include 'config/database.php';



$product_id = intval($_GET['id']);

$product_id = mysqli_real_escape_string($conn, $_GET['id']);

$sql = "SELECT products.*, 
               COALESCE(NULLIF(CONCAT(users.first_name, ' ', users.last_name), ' '), users.username) AS fullname
        FROM products
        JOIN users
        ON products.seller_id = users.id
        WHERE products.id='$product_id'";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){
    $product = mysqli_fetch_assoc($result);
    $product['product_image'] = $product['product_image'] ?? $product['image_url'] ?? '';
    $defaultProductImages = [
        'led-desk-lamp' => 'https://cdn.pixabay.com/photo/2017/12/06/09/31/lamp-2996240_1280.jpg',
        'wooden-coffee-table' => 'https://cdn.pixabay.com/photo/2014/12/16/22/25/table-570399_1280.jpg',
    ];
    $productSlug = $product['slug'] ?? '';
    if (empty($product['product_image']) || $product['product_image'] === 'null') {
        if (!empty($productSlug) && isset($defaultProductImages[$productSlug])) {
            $product['product_image'] = $defaultProductImages[$productSlug];
        } elseif (isset($defaultProductImages[$product['product_name']])) {
            $product['product_image'] = $defaultProductImages[$product['product_name']];
        }
    }
    $product['product_price'] = $product['product_price'] ?? $product['price'] ?? 0;
    $product['product_description'] = $product['product_description'] ?? $product['description'] ?? '';
} else {
    echo "Product Not Found!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
<?php echo htmlspecialchars($product['product_name']); ?>
| NCTraders
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="assets/css/style.css">

<style>

.product-image{
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 10px;
}

</style>

</head>

<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<!-- PRODUCT DETAILS -->

<div class="container py-5" style="margin-top:40px;">

<button onclick="history.back()"
class="btn btn-dark rounded-pill mb-4">

<i class="fas fa-arrow-left"></i>

Back

</button>

<div class="row g-5">

<!-- IMAGE -->

<div class="col-md-6">

<?php if ($product['product_image']): ?>
    <img src="<?php echo htmlspecialchars(strpos($product['product_image'], 'http') === 0 ? $product['product_image'] : 'uploads/products/' . $product['product_image']); ?>"
    class="product-image">
<?php else: ?>
    <div class="bg-white rounded shadow-sm d-flex align-items-center justify-content-center" style="height:500px;">
        <span class="text-muted">No image available</span>
    </div>
<?php endif; ?>

</div>

<!-- PRODUCT INFO -->

<div class="col-md-6">

<h1 class="fw-bold">
<?php echo htmlspecialchars($product['product_name']); ?>
</h1>

<h3 class="text-success mt-3">
R<?php echo number_format($product['product_price'],2); ?>
</h3>

<p class="mt-3">

<strong>Seller:</strong>

<?php echo htmlspecialchars($product['fullname']); ?>

</p>

<hr>

<h5>Description</h5>

<p>

<?php echo nl2br(htmlspecialchars($product['product_description'])); ?>

</p>

<hr>

<div class="d-grid gap-3">

<div class="alert alert-warning">

<i class="fas fa-circle-info me-2"></i>

Meet sellers in safe public places and inspect items before buying.
Payments should only be done once item is received via cash or EFT.   NCTraders is not responsible for any transactions or interactions between buyers and sellers.

</div>

<a href="messages.php?user=<?php echo $product['seller_id']; ?>"
class="btn btn-primary btn-lg">

<i class="fas fa-message"></i>

Message Seller

</a>

</div>

</div>

</div>

</div>

</body>
</html>
