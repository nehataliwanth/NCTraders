<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

$currentUser = getCurrentUser();
$categories = getAllCategories();
$featuredProducts = getFeaturedProducts(8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NC Traders - Buy and Sell Quality Products</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">

            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-store"></i> NC Traders
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <form class="d-flex mx-auto w-50" action="product.php" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products...">
                    <button class="btn btn-warning" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="product.php">Products</a>
                    </li>

                    <?php if ($currentUser && isSeller()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($currentUser && isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/dashboard.php">Admin</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if ($currentUser): ?>
                                <span class="badge bg-danger cart-count">0</span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <?php if ($currentUser): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($currentUser['first_name'] ?? $currentUser['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                                <li><a class="dropdown-item" href="wishlist.php">Wishlist</a></li>
                                <li><a class="dropdown-item" href="messages.php">Messages</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="profile.php">Profile Settings</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>

            </div>
        </div>
    </nav>


    <!-- HERO SECTION -->
    <section class="hero-section text-white d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container text-center">

            <h1 class="display-4 fw-bold">
                Welcome to NC Traders
            </h1>

            <p class="lead">
                Buy and sell quality products from trusted sellers
            </p>

            <div class="mt-4">
                <a href="product.php" class="btn btn-warning btn-lg me-2">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
                <?php if (!$currentUser): ?>
                    <a href="register.php" class="btn btn-light btn-lg">
                        <i class="fas fa-user-tie"></i> Become a Seller
                    </a>
                <?php else: ?>
                    <a href="dashboard.php" class="btn btn-light btn-lg">
                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- CATEGORIES -->
    <section class="py-5">
        <div class="container">

            <h2 class="mb-4 text-center fw-bold">
                Shop by Category
            </h2>

            <?php if (empty($categories)): ?>
                <div class="alert alert-info text-center">
                    <p>No categories available yet.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($categories as $category): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="product.php?category=<?php echo urlencode($category['slug']); ?>" class="text-decoration-none">
                                <div class="category-card p-4 text-center rounded-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; cursor: pointer; transition: transform 0.3s ease;">
                                    <?php if ($category['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($category['image_url']); ?>" alt="<?php echo htmlspecialchars($category['category_name']); ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
                                    <?php else: ?>
                                        <div style="font-size: 3rem; margin-bottom: 1rem;">
                                            <i class="fas fa-box"></i>
                                        </div>
                                    <?php endif; ?>
                                    <h5 class="text-white fw-bold"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="py-5 bg-light">
        <div class="container">

            <h2 class="mb-4 text-center fw-bold">
                Featured Products
            </h2>

            <?php if (empty($featuredProducts)): ?>
                <div class="alert alert-info text-center">
                    <p>No products available yet. Check back soon!</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($featuredProducts as $product): 
                        $rating = getProductRating($product['id']);
                        $seller = getUserById($product['seller_id']);
                    ?>
                        <!-- PRODUCT CARD -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card product-card h-100 shadow-sm" data-product-id="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-name="<?php echo htmlspecialchars($product['product_name']); ?>">

                                <div class="product-image position-relative">
                                    <?php if ($product['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="height: 200px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #999;">
                                            <i class="fas fa-image" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($product['stock'] == 0): ?>
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">Out of Stock</span>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body d-flex flex-column">

                                    <h5 class="card-title">
                                        <?php echo truncateText($product['product_name'], 50); ?>
                                    </h5>

                                    <p class="text-muted small">
                                        Seller: <?php echo htmlspecialchars($seller['username'] ?? 'Unknown'); ?>
                                    </p>

                                    <p class="text-muted small">
                                        <?php 
                                        $avgRating = round($rating['avg_rating'] ?? 0, 1);
                                        for ($i = 0; $i < 5; $i++):
                                            if ($i < $avgRating):
                                                echo '<i class="fas fa-star text-warning"></i> ';
                                            else:
                                                echo '<i class="far fa-star"></i> ';
                                            endif;
                                        endfor;
                                        echo ' (' . ($rating['review_count'] ?? 0) . ')';
                                        ?>
                                    </p>

                                    <p class="card-text text-muted flex-grow-1">
                                        <?php echo truncateText($product['description'] ?? '', 80); ?>
                                    </p>

                                    <h4 class="text-success fw-bold">
                                        <?php echo formatCurrency($product['price']); ?>
                                    </h4>

                                    <div class="d-grid gap-2">
                                        <a href="product.php?slug=<?php echo urlencode($product['slug']); ?>" class="btn btn-dark">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <?php if ($product['stock'] > 0): ?>
                                            <button class="btn btn-warning" onclick="addToCart(this)" 
                                                data-product-id="<?php echo $product['id']; ?>"
                                                data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                data-product-price="<?php echo $product['price']; ?>"
                                                data-product-image="<?php echo htmlspecialchars($product['image_url']); ?>">
                                                <i class="fas fa-cart-plus"></i> Add to Cart
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-5">
                    <a href="product.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-th"></i> View All Products
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">

            <div class="row mb-4">
                <div class="col-md-3">
                    <h5><i class="fas fa-store"></i> NC Traders</h5>
                    <p class="text-muted">Your trusted marketplace for quality products from verified sellers.</p>
                </div>

                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled text-muted">
                        <li><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li><a href="product.php" class="text-decoration-none text-muted">Products</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">About Us</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Contact</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h5>Customer Service</h5>
                    <ul class="list-unstyled text-muted">
                        <li><a href="#" class="text-decoration-none text-muted">Help Center</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Shipping Info</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Returns</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">FAQ</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="d-flex gap-2">
                        <a href="#" class="text-muted"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
            </div>

            <hr class="bg-secondary">

            <div class="text-center text-muted">
                <p>&copy; 2026 NC Traders. All rights reserved.</p>
            </div>

        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <script>
        // Initialize cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>

</body>
</html>
