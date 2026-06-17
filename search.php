<?php

include 'config/database.php';

$searchTerm = trim($_GET['search'] ?? '');

$result = null;

if($searchTerm != ''){

    $escapedSearch = mysqli_real_escape_string(
    $conn,
    $searchTerm
    );

    $sql = "SELECT products.*, users.username AS seller_name

    FROM products

    JOIN users
    ON products.seller_id = users.id

    WHERE

    products.product_name LIKE '%$escapedSearch%'

    OR

    products.product_description LIKE '%$escapedSearch%'

    ORDER BY products.id DESC";

    $result = mysqli_query($conn, $sql);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Search Results | NC Traders
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<div class="container py-5">
<button onclick="history.back()"
class="btn btn-dark rounded-pill mb-4">

<i class="fas fa-arrow-left"></i>

Back

</button>

<h1 class="fw-bold mb-5">

Search Results For:
"<?php echo htmlspecialchars($searchTerm ?: 'No Search Entered'); ?>"

</h1>

<div class="row g-4">

<?php if($result && mysqli_num_rows($result) > 0): ?>

<?php while($row = mysqli_fetch_assoc($result)):

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

<div class="product-status
<?php

if(strtolower(trim($productStatus)) == 'sold out'){

echo ' status-danger';

}
else {

echo ' status-success';

}

?>">

<?php

if(strtolower(trim($productStatus)) == 'sold out'){

echo 'SOLD OUT';

}
else {

echo 'AVAILABLE';

}

?>

</div>

</div>

<div class="product-body">

<h5>

<?php echo htmlspecialchars($row['product_name']); ?>

</h5>

<div class="seller-name">

Seller:
<?php echo htmlspecialchars($row['seller_name']); ?>

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

<?php else: ?>

<div class="text-center">

<h2 class="text-danger">

No Results Found

</h2>

<p>

Try searching for another product.

</p>

</div>

<?php endif; ?>

</div>

</div>

</body>
</html>