content = '''<?php
session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$message = '';

if(isset($_POST['add_product'])){

    $seller_id = $_SESSION['user_id'];

    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);

    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);

    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);

    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    $image_name = $_FILES['product_image']['name'];

    $temp_name = $_FILES['product_image']['tmp_name'];

    $folder = 'uploads/products/' . $image_name;

    move_uploaded_file($temp_name, $folder);

    $sql = "INSERT INTO products
    (seller_id, category_id, product_name, product_description, product_price, product_image)

    VALUES
    ('$seller_id','$category_id','$product_name','$product_description','$product_price','$image_name')";

    if(mysqli_query($conn, $sql)){

        $message = 'Product Added Successfully!';

    } else {

        $message = 'Failed To Add Product!';
    }
}
?>

<!DOCTYPE html>
<html lang='en'>
<head>

<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>

<title>Add Product | NCTraders</title>

<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>

</head>

<body class='bg-light'>

<nav class='navbar navbar-dark bg-dark'>

<div class='container'>

<a href='dashboard.php' class='navbar-brand'>
NCTraders Seller Dashboard
</a>

<a href='logout.php' class='btn btn-warning'>
Logout
</a>

</div>
</nav>

<div class='container py-5'>

<div class='row justify-content-center'>

<div class='col-md-8'>

<div class='card shadow'>

<div class='card-body p-5'>

<h2 class='mb-4 text-center'>
Add Product
</h2>

<?php if($message != ''){ ?>

<div class='alert alert-info'>

<?php echo $message; ?>

</div>

<?php } ?>

<form method='POST' enctype='multipart/form-data'>

<div class='mb-3'>

<label>Product Name</label>

<input type='text'
name='product_name'
class='form-control'
required>

</div>

<div class='mb-3'>

<label>Description</label>

<textarea name='product_description'
class='form-control'
rows='5'
required></textarea>

</div>

<div class='mb-3'>

<label>Price (R)</label>

<input type='number'
name='product_price'
class='form-control'
required>

</div>

<div class='mb-3'>

<label>Category</label>

<select name='category_id' class='form-select'>

<option value='1'>Electronics</option>
<option value='2'>Fashion</option>
<option value='3'>Sneakers</option>
<option value='4'>Furniture</option>

</select>

</div>

<div class='mb-3'>

<label>Product Image</label>

<input type='file'
name='product_image'
class='form-control'
required>

</div>

<button type='submit'
name='add_product'
class='btn btn-dark w-100'>

Upload Product

</button>

</form>

</div>
</div>
</div>
</div>
</div>

</body>
</html>
'''
with open(r'c:\xampp\htdocs\nctraders\add-product.php', 'w', encoding='utf-8') as f:
    f.write(content)
