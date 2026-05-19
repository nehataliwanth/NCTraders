<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

$currentUser = getCurrentUser();
$categories = getAllCategories();

// Get filters
$categorySlug = $_GET['category'] ?? '';
$searchTerm = $_GET['search'] ?? '';
$sortBy = $_GET['sort'] ?? 'newest';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$where = "WHERE p.status = 'active'";
$params = [];

if (!empty($categorySlug)) {
    $where .= " AND c.slug = '" . escapeString($categorySlug) . "'";
}

if (!empty($searchTerm)) {
    $where .= " AND (p.product_name LIKE '%" . escapeString($searchTerm) . "%' OR p.description LIKE '%" . escapeString($searchTerm) . "%')";
}

// Sorting
$orderBy = "p.created_at DESC";
switch ($sortBy) {
    case 'price-low':
        $orderBy = "p.price ASC";
        break;
    case 'price-high':
        $orderBy = "p.price DESC";
        break;
    case 'popular':
        $orderBy = "p.review_count DESC, p.rating DESC";
        break;
    case 'rating':
        $orderBy = "p.rating DESC";
        break;
}

// Get products
$products = getAllRows("
    SELECT p.* FROM products p
    JOIN categories c ON p.category_id = c.id
    $where
    ORDER BY $orderBy
    LIMIT $limit OFFSET $offset
");

// Get total count
$countResult = getRow("
    SELECT COUNT(*) as total FROM products p
    JOIN categories c ON p.category_id = c.id
    $where
");
$totalProducts = $countResult['total'] ?? 0;
$totalPages = ceil($totalProducts / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - NC Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Navbar -->
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
                    <input class="form-control me-2" type="search" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
                </form>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <?php if ($currentUser): ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 mb-5">
        <div class="row">

            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-filter"></i> Filters</h5>
                    </div>
                    <div class="card-body">

                        <!-- Category Filter -->
                        <h6 class="fw-bold mb-3">Categories</h6>
                        <div class="list-group mb-4">
                            <a href="product.php" class="list-group-item list-group-item-action <?php echo empty($categorySlug) ? 'active' : ''; ?>">
                                All Categories
                            </a>
                            <?php foreach ($categories as $cat): ?>
                                <a href="product.php?category=<?php echo urlencode($cat['slug']); ?>" 
                                   class="list-group-item list-group-item-action <?php echo $categorySlug === $cat['slug'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <!-- Sort Filter -->
                        <h6 class="fw-bold mb-3">Sort By</h6>
                        <div class="list-group">
                            <a href="product.php?sort=newest<?php echo !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : ''; ?>" 
                               class="list-group-item list-group-item-action <?php echo $sortBy === 'newest' ? 'active' : ''; ?>">
                                Newest
                            </a>
                            <a href="product.php?sort=price-low<?php echo !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : ''; ?>" 
                               class="list-group-item list-group-item-action <?php echo $sortBy === 'price-low' ? 'active' : ''; ?>">
                                Price: Low to High
                            </a>
                            <a href="product.php?sort=price-high<?php echo !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : ''; ?>" 
                               class="list-group-item list-group-item-action <?php echo $sortBy === 'price-high' ? 'active' : ''; ?>">
                                Price: High to Low
                            </a>
                            <a href="product.php?sort=popular<?php echo !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : ''; ?>" 
                               class="list-group-item list-group-item-action <?php echo $sortBy === 'popular' ? 'active' : ''; ?>">
                                Most Popular
                            </a>
                            <a href="product.php?sort=rating<?php echo !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : ''; ?>" 
                               class="list-group-item list-group-item-action <?php echo $sortBy === 'rating' ? 'active' : ''; ?>">
                                Highest Rated
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">

                <!-- Results Header -->
                <div class="mb-4">
                    <h2 class="fw-bold">Products</h2>
                    <p class="text-muted">
                        Showing <strong><?php echo count($products); ?></strong> of <strong><?php echo $totalProducts; ?></strong> products
                        <?php if (!empty($searchTerm)): ?>
                            for "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>"
                        <?php endif; ?>
                    </p>
                </div>

                <?php if (empty($products)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No products found matching your criteria.
                    </div>
                <?php else: ?>
                    <!-- Products Grid -->
                    <div class="row g-4 mb-4">
                        <?php foreach ($products as $product):
                            $rating = getProductRating($product['id']);
                            $seller = getUserById($product['seller_id']);
                        ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card product-card h-100 shadow-sm">

                                    <div class="position-relative">
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
                                            by <strong><?php echo htmlspecialchars($seller['username'] ?? 'Unknown'); ?></strong>
                                        </p>
                                        <p class="text-muted small mb-2">
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
                                        <p class="card-text text-muted flex-grow-1 small">
                                            <?php echo truncateText($product['description'] ?? '', 80); ?>
                                        </p>
                                        <h4 class="text-success fw-bold">
                                            <?php echo formatCurrency($product['price']); ?>
                                        </h4>
                                        <div class="d-grid gap-2">
                                            <a href="#product-detail-<?php echo $product['id']; ?>" class="btn btn-dark btn-sm" data-bs-toggle="modal">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <?php if ($product['stock'] > 0): ?>
                                                <button class="btn btn-warning btn-sm" onclick="addToCart(this)"
                                                    data-product-id="<?php echo $product['id']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                    data-product-price="<?php echo $product['price']; ?>"
                                                    data-product-image="<?php echo htmlspecialchars($product['image_url']); ?>">
                                                    <i class="fas fa-cart-plus"></i> Add
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-4">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="product.php?page=<?php echo $page - 1; ?><?php echo !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>">
                                            Previous
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="product.php?page=<?php echo $i; ?><?php echo !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="product.php?page=<?php echo $page + 1; ?><?php echo !empty($categorySlug) ? '&category=' . urlencode($categorySlug) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>">
                                            Next
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 NC Traders. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
