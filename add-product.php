<?php
session_start();

include 'config/database.php';

/* LOGIN CHECK */

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();
}

$message = "";

/* CREATE product_status COLUMN IF MISSING */

$checkColumn = mysqli_query(
$conn,
"SHOW COLUMNS FROM products LIKE 'product_status'"
);

if(mysqli_num_rows($checkColumn) == 0){

    mysqli_query(
    $conn,
    "ALTER TABLE products
    ADD COLUMN product_status VARCHAR(50)
    DEFAULT 'Available'"
    );
}

/* ADD PRODUCT */

if(isset($_POST['add_product'])){

    $seller_id = $_SESSION['user_id'];

    $product_name = mysqli_real_escape_string(
    $conn,
    $_POST['product_name']
    );

    $product_description = mysqli_real_escape_string(
    $conn,
    $_POST['product_description']
    );

    $price = mysqli_real_escape_string(
    $conn,
    $_POST['price']
    );

    $category_id = mysqli_real_escape_string(
    $conn,
    $_POST['category_id']
    );

    $product_status = mysqli_real_escape_string(
    $conn,
    $_POST['product_status']
    );

    /* IMAGE */

    $image_name =
    $_FILES['product_image']['name'];

    $temp_name =
    $_FILES['product_image']['tmp_name'];

    $image_size =
    $_FILES['product_image']['size'];

    $image_ext = strtolower(
    pathinfo($image_name, PATHINFO_EXTENSION)
    );

    $allowed = ['jpg','jpeg','png','webp'];

    /* VALIDATION */

    if(empty($product_name)
    || empty($product_description)
    || empty($price)){

        $message =
        "Please fill in all fields.";

    }

    elseif(!in_array($image_ext, $allowed)){

        $message =
        "Only JPG, JPEG, PNG and WEBP allowed.";

    }

    elseif($image_size > 5000000){

        $message =
        "Image size too large.";

    }

    else {

        /* RENAME IMAGE */

        $new_image_name =
        time() . "_" . basename($image_name);

        $upload_path =
        "uploads/products/" . $new_image_name;

        move_uploaded_file(
        $temp_name,
        $upload_path
        );

        /* INSERT */

        $sql = "INSERT INTO products
        (
        seller_id,
        category_id,
        product_name,
        product_description,
        price,
        image_url,
        product_status
        )

        VALUES
        (
        '$seller_id',
        '$category_id',
        '$product_name',
        '$product_description',
        '$price',
        '$new_image_name',
        '$product_status'
        )";

        $insert = mysqli_query($conn, $sql);

        if($insert){

            $message =
            "Product uploaded successfully with status: "
            . $product_status;

        } else {

            $message =
            "Database Error: "
            . mysqli_error($conn);
        }
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
Add Product | NC Traders
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

<body>

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

Add New Product

</h2>

<p class="text-center text-muted mb-5">

Upload products to the NC Traders marketplace.

</p>

<?php if($message != ""){ ?>

<div class="alert alert-info text-center">

<?php echo $message; ?>

</div>

<?php } ?>

<form method="POST"
enctype="multipart/form-data">

<!-- PRODUCT NAME -->

<div class="mb-3">

<label class="form-label fw-bold">

Product Name

</label>

<input type="text"
name="product_name"
class="form-control"
placeholder="Enter product name"
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
placeholder="Describe your product..."
required></textarea>

</div>

<!-- PRICE -->

<div class="mb-3">

<label class="form-label fw-bold">

Price (R)

</label>

<input type="number"
name="price"
class="form-control"
placeholder="Enter product price"
required>

</div>

<!-- CATEGORY -->

<div class="mb-3">

<label class="form-label fw-bold">

Category

</label>

<select name="category_id"
class="form-select">

<option value="1">

Electronics

</option>

<option value="2">

Fashion

</option>

<option value="3">

Sneakers

</option>

<option value="4">

Furniture

</option>

</select>

</div>

<!-- PRODUCT STATUS -->

<div class="mb-3">

<label class="form-label fw-bold">

Product Status

</label>

<select name="product_status"
class="form-select">

<option value="Available">

Available

</option>

<option value="Sold Out">

Sold Out

</option>

</select>

</div>

<!-- IMAGE -->

<div class="mb-4">

<!-- LOCATION -->

<div class="mb-4">

<label class="form-label fw-bold">

Suburb & City

</label>

<input type="text"
name="location"
class="form-control"
list="southAfricaLocations"
placeholder="Enter suburb and city"
autocomplete="off">

<datalist id="southAfricaLocations">

<option value="Sandton, Johannesburg">
<option value="Soweto, Johannesburg">
<option value="Rosebank, Johannesburg">
<option value="Midrand, Johannesburg">
<option value="Pretoria Central, Pretoria">
<option value="Centurion, Pretoria">
<option value="Hatfield, Pretoria">

<option value="Umhlanga, Durban">
<option value="Chatsworth, Durban">
<option value="Phoenix, Durban">
<option value="Ballito, Durban">

<option value="Cape Town CBD, Cape Town">
<option value="Bellville, Cape Town">
<option value="Mitchells Plain, Cape Town">
<option value="Claremont, Cape Town">

<option value="Bloemfontein Central, Bloemfontein">
<option value="Polokwane Central, Polokwane">
<option value="Nelspruit, Mbombela">
<option value="Kimberley Central, Kimberley">

<option value="Potchefstroom, North West">
<option value="Rustenburg, North West">
<option value="Welkom, Free State">
<option value="Sasolburg, Free State">

</datalist>

<small class="text-muted">

Only your suburb and city will be shown publicly for safety.

</small>

</div>

<!-- PRODUCT IMAGE -->

<div class="mb-4">

<label class="form-label fw-bold">

Product Image

</label>

<input type="file"
name="product_image"
class="form-control"
required>

<small class="text-muted">

Allowed: JPG, JPEG, PNG, WEBP

</small>

</div>

<small class="text-muted">

Allowed:
JPG, JPEG, PNG, WEBP

</small>

</div>

<!-- SUBMIT -->

<button type="submit"
name="add_product"
class="btn btn-dark w-100">

Upload Product

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>f