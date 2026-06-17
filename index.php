<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';
include 'config/database.php';

$currentUser = getCurrentUser(); 
/* UNREAD MESSAGES */

$unreadMessages = 0;

if($currentUser){

    $userId = $currentUser['id'];

    $messageQuery = mysqli_query($conn,

    "SELECT COUNT(*) AS total

    FROM messages

    WHERE receiver_id='$userId'

    AND is_read='0'"

    );

    $messageData = mysqli_fetch_assoc($messageQuery);

    $unreadMessages = $messageData['total'];
}
$categories = getAllCategories();

if($searchTerm != ""){

    $escapedSearch = mysqli_real_escape_string(
    $conn,
    $searchTerm
    );

    $whereSql = "

    WHERE

    products.product_name LIKE '%$escapedSearch%'

    OR

    products.product_description LIKE '%$escapedSearch%'

    ";
}

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

<title>NC Traders</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
<?php include 'includes/navbar.php'; ?>
<!-- HERO -->

<section class="hero-section" style="margin-top:-90px;">

<div class="hero-overlay"></div>

<div class="container hero-content">

<div class="row align-items-center">

<div class="col-lg-6">

<div class="hero-badge">
South African Marketplace Platform
</div>

<h1 class="hero-title">
Buy & Sell Locally with Confidence
</h1>

<p class="hero-text">
Empowering township entrepreneurs, side hustlers,
and everyday South Africans through safe online trading.
</p>

<div class="hero-buttons">

<a href="add-product.php" class="btn btn-warning btn-lg hero-btn">

<i class="fas fa-store"></i>

Start Selling

</a>

<a href="products.php" class="btn btn-light btn-lg hero-btn-secondary">

<i class="fas fa-bag-shopping"></i>

Browse Products

</a>

</div>

<div class="hero-stats mt-5">

<div class="stat-card">
<h3>5K+</h3>
<p>Products</p>
</div>

<div class="stat-card">
<h3>2K+</h3>
<p>Sellers</p>
</div>

<div class="stat-card">
<h3>100%</h3>
<p>Local</p>
</div>

</div>

</div>
<div class="col-lg-6">

<div class="floating-market-card">

<img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=1200&auto=format&fit=crop"
class="market-image">

</div>

</div>

</div>

</div>

</section>

<!-- FEATURES -->

<section class="features-section">

<div class="container">

<div class="row g-4">

<div class="col-md-4">

<div class="feature-box">

<div class="feature-icon">
<i class="fas fa-shield"></i>
</div>

<h4>Secure Marketplace</h4>

<p>
Safer buying and selling with trusted product listings.
</p>

</div>

</div>

<div class="col-md-4">

<div class="feature-box">

<div class="feature-icon">
<i class="fas fa-bolt"></i>
</div>

<h4>Fast Selling</h4>

<p>
Upload products and start selling instantly online.
</p>

</div>

</div>

<div class="col-md-4">

<div class="feature-box">

<div class="feature-icon">
<i class="fas fa-users"></i>
</div>

<h4>Built For Locals</h4>

<p>
Created specifically for South African township businesses.
</p>

</div>

</div>

</div>

</div>

</section>

<footer class="footer-section text-center">

<div class="container">

<img src="assets/images/logo.png"
style="
width:250px;
height:250px;
object-fit:contain;
margin-bottom:25px;
">

<h2 class="fw-bold mb-3 text-white">

NC Traders

</h2>

<p class="footer-slogan mb-4">

South Africa's trusted digital marketplace for South African entrepreneurs.

</p>

<hr class="border-secondary">

<p class="mt-4 mb-0 text-white">

© 2026 NC Traders. All Rights Reserved.

</p>

</div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

const searchInput = document.getElementById("searchInput");

const historyBox = document.getElementById("searchHistory");

let searches =
JSON.parse(localStorage.getItem("searchHistory")) || [];

function renderHistory(){

historyBox.innerHTML = "";

if(searches.length === 0){

historyBox.classList.add("d-none");

return;

}

searches.forEach((item,index)=>{

historyBox.innerHTML += `

<div class="search-history-item">

<span onclick="selectSearch('${item}')">

${item}

</span>

<span class="search-remove"
onclick="removeSearch(${index})">

✕

</span>

</div>

`;

});

historyBox.classList.remove("d-none");
}

searchInput.addEventListener("focus",()=>{

renderHistory();

});

document.addEventListener("click",(e)=>{

if(!e.target.closest(".search-wrapper")){

historyBox.classList.add("d-none");

}

});

function selectSearch(value){

searchInput.value = value;

historyBox.classList.add("d-none");
}

function removeSearch(index){

searches.splice(index,1);

localStorage.setItem(
"searchHistory",
JSON.stringify(searches)
);

renderHistory();
}

document.querySelector(".search-form")
.addEventListener("submit",(e)=>{

const value = searchInput.value.trim();

if(value !== ""){

searches = searches.filter(
item => item !== value
);

searches.unshift(value);

searches = searches.slice(0,8);

localStorage.setItem(
"searchHistory",
JSON.stringify(searches)
);
}

});

</script>
</body>
</html>