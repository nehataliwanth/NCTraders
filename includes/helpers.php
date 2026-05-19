<?php
// Helper Functions

// Format currency
function formatCurrency($amount) {
    return 'R' . number_format((float)$amount, 2);
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    return date('M d, Y g:i A', strtotime($datetime));
}

// Truncate text
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Generate slug
function generateSlug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Upload file
function uploadFile($file, $directory = 'uploads/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload failed'];
    }
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $fileName = basename($file['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    if ($file['size'] > 5000000) { // 5MB limit
        return ['success' => false, 'message' => 'File is too large'];
    }
    
    $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $directory . $newFileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => true, 'file_path' => $filePath, 'file_name' => $newFileName];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

// Get average rating for product
function getProductRating($productId) {
    $result = getRow("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE product_id = $productId AND status = 'approved'");
    return $result;
}

// Get cart count
function getCartCount($userId) {
    $result = getRow("SELECT SUM(quantity) as total FROM cart WHERE user_id = $userId");
    return $result['total'] ?? 0;
}

// Get cart items
function getCartItems($userId) {
    return getAllRows("
        SELECT c.*, p.product_name, p.price, p.image_url 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $userId
    ");
}

// Calculate cart total
function getCartTotal($userId) {
    $result = getRow("
        SELECT SUM(c.quantity * p.price) as total 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $userId
    ");
    return $result['total'] ?? 0;
}

// Get featured products
function getFeaturedProducts($limit = 8) {
    return getAllRows("
        SELECT * FROM products 
        WHERE status = 'active' 
        ORDER BY review_count DESC, rating DESC
        LIMIT $limit
    ");
}

// Get products by category
function getProductsByCategory($categoryId, $limit = 12, $offset = 0) {
    return getAllRows("
        SELECT * FROM products 
        WHERE category_id = $categoryId AND status = 'active'
        LIMIT $limit OFFSET $offset
    ");
}

// Get all categories
function getAllCategories() {
    return getAllRows("SELECT * FROM categories ORDER BY category_name ASC");
}

// Get category by slug
function getCategoryBySlug($slug) {
    return getRow("SELECT * FROM categories WHERE slug = '" . escapeString($slug) . "'");
}

// Get product by slug
function getProductBySlug($slug) {
    return getRow("SELECT * FROM products WHERE slug = '" . escapeString($slug) . "' AND status = 'active'");
}

// Get seller info
function getSellerInfo($sellerId) {
    return getRow("
        SELECT u.*, COUNT(DISTINCT p.id) as product_count, AVG(p.rating) as avg_rating
        FROM users u 
        LEFT JOIN products p ON u.id = p.seller_id
        WHERE u.id = $sellerId AND u.role_id = 3
        GROUP BY u.id
    ");
}

// Get order by ID
function getOrderById($orderId) {
    return getRow("SELECT * FROM orders WHERE id = $orderId");
}

// Get order items
function getOrderItems($orderId) {
    return getAllRows("SELECT * FROM order_items WHERE order_id = $orderId");
}

// Generate order number
function generateOrderNumber() {
    return 'ORD' . date('Ymd') . strtoupper(uniqid());
}

// Get user orders
function getUserOrders($userId) {
    return getAllRows("SELECT * FROM orders WHERE user_id = $userId ORDER BY created_at DESC");
}

// Get seller products
function getSellerProducts($sellerId) {
    return getAllRows("SELECT * FROM products WHERE seller_id = $sellerId ORDER BY created_at DESC");
}

// Get seller orders
function getSellerOrders($sellerId) {
    return getAllRows("
        SELECT DISTINCT o.* 
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        WHERE oi.seller_id = $sellerId 
        ORDER BY o.created_at DESC
    ");
}

// Get wishlist
function getWishlist($userId) {
    return getAllRows("
        SELECT p.* FROM products p 
        JOIN wishlist w ON p.id = w.product_id 
        WHERE w.user_id = $userId
    ");
}

// Check if product is in wishlist
function isInWishlist($userId, $productId) {
    $result = getRow("SELECT id FROM wishlist WHERE user_id = $userId AND product_id = $productId");
    return $result ? true : false;
}

// Add to wishlist
function addToWishlist($userId, $productId) {
    $data = ['user_id' => $userId, 'product_id' => $productId];
    return insert('wishlist', $data);
}

// Remove from wishlist
function removeFromWishlist($userId, $productId) {
    return delete('wishlist', ['user_id' => $userId, 'product_id' => $productId]);
}

// Get unread message count
function getUnreadMessageCount($userId) {
    $result = getRow("SELECT COUNT(*) as count FROM messages WHERE receiver_id = $userId AND is_read = FALSE");
    return $result['count'] ?? 0;
}

// Get user messages
function getUserMessages($userId) {
    return getAllRows("SELECT * FROM messages WHERE receiver_id = $userId ORDER BY created_at DESC");
}

// Get conversations
function getConversations($userId) {
    return getAllRows("
        SELECT DISTINCT u.*, m.id as last_message_id, m.message_text as last_message, m.created_at as last_message_time
        FROM users u
        JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id)
        WHERE (m.sender_id = $userId OR m.receiver_id = $userId)
        AND u.id != $userId
        ORDER BY m.created_at DESC
        GROUP BY u.id
    ");
}

// Get conversation messages
function getConversationMessages($userId, $otherUserId) {
    return getAllRows("
        SELECT * FROM messages 
        WHERE (sender_id = $userId AND receiver_id = $otherUserId) 
        OR (sender_id = $otherUserId AND receiver_id = $userId)
        ORDER BY created_at ASC
    ");
}

// Send message
function sendMessage($senderId, $receiverId, $message, $productId = null) {
    $data = [
        'sender_id' => $senderId,
        'receiver_id' => $receiverId,
        'message_text' => $message,
        'product_id' => $productId
    ];
    return insert('messages', $data);
}

// Mark message as read
function markMessageAsRead($messageId) {
    return update('messages', ['is_read' => 1], ['id' => $messageId]);
}
?>
