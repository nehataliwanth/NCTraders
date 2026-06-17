<?php
session_start();

include 'config/database.php';

/* LOGIN CHECK */

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();
}

/* VALIDATE PRODUCT ID */

if(!isset($_GET['id'])){

    header("Location: manage-products.php");
    exit();
}

$id = intval($_GET['id']);

/* GET PRODUCT */

$query = mysqli_query(
$conn,
"SELECT * FROM products WHERE id='$id'"
);

$product = mysqli_fetch_assoc($query);

if(!$product){

    die("Product not found.");
}

$message = "";

/* UPDATE PRODUCT */

if(isset($_POST['update_product'])){

    $name = mysqli_real_escape_string(
    $conn,
    $_POST['product_name']
    );

    $description = mysqli_real_escape_string(
    $conn,
    $_POST['product_description']
    );

    $price = mysqli_real_escape_string(
    $conn,
    $_POST['product_price']
    );

    $product_status = mysqli_real_escape_string(
    $conn,
    $_POST['product_status']
    );

    /* UPDATE QUERY */

    $update = "UPDATE products SET

    product_name='$name',

    product_description='$description',

    price='$price',

    product_status='$product_status'

    WHERE id='$id'";

    if(mysqli_query($conn, $update)){

        $message =
        "Product Updated Successfully!";

        /* REFRESH PRODUCT DATA */

        $query = mysqli_query(
        $conn,
        "SELECT * FROM products WHERE id='$id'"
        );

        $product = mysqli_fetch_assoc($query);

    } else {

        $message =
        "Database Error: "
        . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Edit Product | NC Traders
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="assets/css/style.css">

<style>

body{

    background:#f5f5f5;
}

.card{

    border:none;
    border-radius:15px;
}

.form-control,
.form-select{

    border-radius:10px;
}

.btn-dark{

    border-radius:10px;
    padding:12px;
}

</style>

</head>

<body class="bg-light">

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container">

<a href="dashboard.php"
class="navbar-brand d-flex align-items-center">

<img src="assets/images/logo.png"
width="40"
height="40"
class="rounded-circle me-2">

<div>

<div class="fw-bold">

NC Traders

</div>

<small style="font-size:11px; color:#ffc107;">

Trade Smart. Grow Local.

</small>

</div>

</a>

<div>

<a href="manage-products.php"
class="btn btn-outline-light me-2">

Manage Products

</a>

<a href="logout.php"
class="btn btn-warning">

Logout

</a>

</div>

</div>

</nav>

<!-- PAGE -->

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow-lg">

<div class="card-body p-5">

<h2 class="mb-4 text-center fw-bold">

Edit Product

</h2>

<p class="text-center text-muted mb-5">

Update your product information and status.

</p>

<?php if($message != ""){ ?>

<div class="alert alert-info text-center">

<?php echo $message; ?>

</div>

<?php } ?>

<form method="POST">

<!-- PRODUCT NAME -->

<div class="mb-3">

<label class="form-label fw-bold">

Product Name

</label>

<input type="text"
name="product_name"
class="form-control"
value="<?php echo htmlspecialchars($product['product_name']); ?>"
required>

</div>

<!-- DESCRIPTION -->

<div class="mb-3">

<label class="form-label fw-bold">

Description

</label>

<textarea name="product_description"
class="form-control"
rows="5"
required><?php echo htmlspecialchars($product['product_description']); ?></textarea>

</div>

<!-- PRICE -->

<div class="mb-3">

<label class="form-label fw-bold">

Price (R)

</label>

<input type="number"
name="product_price"
class="form-control"
value="<?php echo htmlspecialchars($product['price']); ?>"
required>

</div>

<!-- PRODUCT STATUS -->

<div class="mb-4">

<label class="form-label fw-bold">

Product Status

</label>

<select name="product_status"
class="form-select">

<option value="Active"
<?php if(($product['product_status'] ?? '') == 'Active') echo 'selected'; ?>>

Active

</option>

<option value="Out of Stock"
<?php if(($product['product_status'] ?? '') == 'Out of Stock') echo 'selected'; ?>>

Out of Stock

</option>

<option value="Inactive"
<?php if(($product['product_status'] ?? '') == 'Inactive') echo 'selected'; ?>>

Inactive

</option>

</select>

</div>

<!-- SUBMIT -->

<button type="submit"
name="update_product"
class="btn btn-dark w-100">

Update Product

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>