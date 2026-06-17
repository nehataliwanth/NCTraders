<?php

session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){

header("Location: login.php");
exit();

}

$user_id = $_SESSION['user_id'];

/* UPDATE STATUS */
/* DELETE LISTING */

if(isset($_POST['delete_listing'])){

$product_id =
$_POST['product_id'];

/* DELETE ORDER ITEMS FIRST */

mysqli_query($conn,

"DELETE FROM order_items

WHERE product_id='$product_id'");

/* DELETE PRODUCT */

mysqli_query($conn,

"DELETE FROM products

WHERE id='$product_id'

AND seller_id='$user_id'");

header("Location: my_listings.php");
exit();

}

if(isset($_POST['update_status'])){

$product_id =
$_POST['product_id'];

$status =
$_POST['status'];

mysqli_query($conn,

"UPDATE products

SET product_status='$status'

WHERE id='$product_id'

AND seller_id='$user_id'");

}

/* GET PRODUCTS */

$query = mysqli_query($conn,

"SELECT *

FROM products

WHERE seller_id='$user_id'

ORDER BY id DESC");

?>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
My Listings
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f5f5f5;
font-family:Arial,sans-serif;
}

.listings-container{
max-width:1200px;
margin:40px auto;
padding:20px;
}

.page-title{
font-size:34px;
font-weight:700;
margin-bottom:30px;
}

.listing-card{
background:white;
border-radius:18px;
padding:20px;
margin-bottom:20px;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
display:flex;
gap:20px;
align-items:center;
}

.product-image{
width:130px;
height:130px;
object-fit:cover;
border-radius:14px;
}

.product-info{
flex:1;
}

.product-title{
font-size:22px;
font-weight:700;
margin-bottom:8px;
}

.product-price{
font-size:18px;
font-weight:600;
color:#16a34a;
margin-bottom:10px;
}

.status-form{
display:flex;
gap:12px;
align-items:center;
margin-top:15px;
}

.status-select{
padding:10px 14px;
border-radius:12px;
border:1px solid #ccc;
}

.save-btn{
background:#162331;
color:white;
border:none;
padding:10px 18px;
border-radius:12px;
font-weight:600;
}

.delete-btn{
background:#e63946;
color:white;
border:none;
padding:10px 18px;
border-radius:12px;
font-weight:600;
cursor:pointer;
}

</style>

</head>

<body>

<div class="listings-container">

<div class="page-title">

My Listings

</div>

<?php while($product = mysqli_fetch_assoc($query)): ?>

<div class="listing-card">

<img src="<?php echo $product['image_url']; ?>"
class="product-image">

<div class="product-info">

<div class="product-title">

<?php echo htmlspecialchars($product['product_name']); ?>

</div>

<div class="product-price">

R <?php echo number_format($product['price'],2); ?>

</div>

<div>

Current Status:

<strong>

<?php echo $product['product_status']; ?>

</strong>

</div>

<form method="POST"
class="status-form">

<input type="hidden"
name="product_id"
value="<?php echo $product['id']; ?>">

<select name="status"
class="status-select">

<option value="Available"
<?php if($product['product_status']=="Available") echo "selected"; ?>>

Available

</option>

<option value="Sold Out"
<?php if($product['product_status']=="Sold Out") echo "selected"; ?>>

Sold Out

</option>

</select>

<div style="display:flex; gap:10px;">

<button type="submit"
name="update_status"
class="save-btn">

Save

</button>

<button type="submit"
name="delete_listing"
class="delete-btn"
onclick="return confirm('Delete this listing?')">

Delete

</button>

</div>

</form>

</div>

</div>

<?php endwhile; ?>

</div>

</body>

</html>