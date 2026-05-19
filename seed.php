<?php
// Sample Data Seeding Script

require_once 'config/db.php';
require_once 'includes/helpers.php';

echo "<h2>Seeding Sample Data...</h2>";

// Insert Categories
$categories = [
    [
        'category_name' => 'Electronics',
        'slug' => 'electronics',
        'description' => 'Smartphones, laptops, tablets, and accessories'
    ],
    [
        'category_name' => 'Fashion',
        'slug' => 'fashion',
        'description' => 'Clothing, shoes, and accessories for men and women'
    ],
    [
        'category_name' => 'Home & Garden',
        'slug' => 'home-garden',
        'description' => 'Furniture, decor, and home improvement products'
    ],
    [
        'category_name' => 'Beauty & Personal Care',
        'slug' => 'beauty-personal-care',
        'description' => 'Cosmetics, skincare, and personal care products'
    ],
    [
        'category_name' => 'Books & Media',
        'slug' => 'books-media',
        'description' => 'Books, eBooks, DVDs, and digital media'
    ],
    [
        'category_name' => 'Sports & Outdoors',
        'slug' => 'sports-outdoors',
        'description' => 'Sports equipment, fitness gear, and outdoor products'
    ]
];

echo "<h3>Adding Categories:</h3>";
foreach ($categories as $cat) {
    $existingCat = getRow("SELECT id FROM categories WHERE slug = '" . escapeString($cat['slug']) . "'");
    if (!$existingCat) {
        $catId = insert('categories', $cat);
        echo "✓ Added category: {$cat['category_name']}<br>";
    } else {
        echo "• Category already exists: {$cat['category_name']}<br>";
    }
}

// Create a demo seller user if not exists
$demoSeller = getRow("SELECT id FROM users WHERE email = 'seller@example.com'");
if (!$demoSeller) {
    $sellerData = [
        'username' => 'demoseller',
        'email' => 'seller@example.com',
        'password' => password_hash('demo123', PASSWORD_BCRYPT),
        'first_name' => 'Demo',
        'last_name' => 'Seller',
        'role_id' => 3, // Seller role
        'status' => 'active'
    ];
    $sellerId = insert('users', $sellerData);
    echo "<br><h3>Created Demo Seller Account:</h3>";
    echo "✓ Username: demoseller<br>";
    echo "✓ Email: seller@example.com<br>";
    echo "✓ Password: demo123<br>";
} else {
    $sellerId = $demoSeller['id'];
    echo "<br><h3>Demo Seller Account Already Exists</h3>";
}

// Get category IDs for inserting products
$electronicsCategory = getRow("SELECT id FROM categories WHERE slug = 'electronics'");
$fashionCategory = getRow("SELECT id FROM categories WHERE slug = 'fashion'");
$homeCategory = getRow("SELECT id FROM categories WHERE slug = 'home-garden'");

// Insert Sample Products
$products = [
    [
        'seller_id' => $sellerId,
        'category_id' => $electronicsCategory['id'],
        'product_name' => 'Wireless Bluetooth Headphones',
        'slug' => 'wireless-bluetooth-headphones',
        'description' => 'Premium quality wireless headphones with noise cancellation and 30-hour battery life',
        'price' => 2500.00,
        'stock' => 15,
        'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400'
    ],
    [
        'seller_id' => $sellerId,
        'category_id' => $electronicsCategory['id'],
        'product_name' => 'Smartphone Stand',
        'slug' => 'smartphone-stand',
        'description' => 'Adjustable phone stand for desk, perfect for video calls and streaming',
        'price' => 450.00,
        'stock' => 30,
        'image_url' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=400'
    ],
    [
        'seller_id' => $sellerId,
        'category_id' => $fashionCategory['id'],
        'product_name' => 'Classic White T-Shirt',
        'slug' => 'classic-white-tshirt',
        'description' => '100% cotton classic white t-shirt, comfortable and versatile',
        'price' => 350.00,
        'stock' => 50,
        'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400'
    ],
    [
        'seller_id' => $sellerId,
        'category_id' => $fashionCategory['id'],
        'product_name' => 'Running Shoes',
        'slug' => 'running-shoes',
        'description' => 'Professional running shoes with cushioned sole and breathable mesh',
        'price' => 1800.00,
        'stock' => 20,
        'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400'
    ],
    [
        'seller_id' => $sellerId,
        'category_id' => $homeCategory['id'],
        'product_name' => 'Wooden Coffee Table',
        'slug' => 'wooden-coffee-table',
        'description' => 'Beautiful solid wood coffee table with modern design',
        'price' => 4500.00,
        'stock' => 8,
        'image_url' => 'https://images.unsplash.com/photo-1533090161767-e6ffb817ba2b?w=400'
    ],
    [
        'seller_id' => $sellerId,
        'category_id' => $homeCategory['id'],
        'product_name' => 'LED Desk Lamp',
        'slug' => 'led-desk-lamp',
        'description' => 'Modern LED desk lamp with adjustable brightness and color temperature',
        'price' => 1200.00,
        'stock' => 25,
        'image_url' => 'https://images.unsplash.com/photo-1565636192335-14e9952f765e?w=400'
    ],
    [
        'seller_id' => $sellerId,
        'category_id' => $electronicsCategory['id'],
        'product_name' => 'USB-C Fast Charging Cable',
        'slug' => 'usb-c-fast-charging-cable',
        'description' => '2-meter USB-C cable with fast charging support, durable and reliable',
        'price' => 250.00,
        'stock' => 100,
        'image_url' => 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400'
    ],
    [
        'seller_id' => $sellerId,
        'category_id' => $fashionCategory['id'],
        'product_name' => 'Sports Backpack',
        'slug' => 'sports-backpack',
        'description' => 'Waterproof sports backpack with multiple compartments and USB charging port',
        'price' => 1500.00,
        'stock' => 18,
        'image_url' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400'
    ]
];

echo "<br><h3>Adding Sample Products:</h3>";
foreach ($products as $product) {
    $existingProduct = getRow("SELECT id FROM products WHERE slug = '" . escapeString($product['slug']) . "'");
    if (!$existingProduct) {
        $productId = insert('products', $product);
        echo "✓ Added product: {$product['product_name']}<br>";
    } else {
        echo "• Product already exists: {$product['product_name']}<br>";
    }
}

echo "<br><h2 style='color: green;'>✓ Sample data seeding completed!</h2>";
echo "<p><a href='index.php' style='color: blue; text-decoration: none;'>&larr; Go to Homepage</a></p>";
?>
