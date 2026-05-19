<?php
// Session Configuration
session_start();

require_once __DIR__ . '/db.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current logged-in user
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    $userId = $_SESSION['user_id'];
    return getRow("SELECT * FROM users WHERE id = $userId");
}

// Get user by ID
function getUserById($userId) {
    return getRow("SELECT * FROM users WHERE id = $userId");
}

// Check if user has a specific role
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    $user = getCurrentUser();
    if (!$user) {
        return false;
    }
    $roleData = getRow("SELECT id FROM roles WHERE role_name = '$role' AND id = " . $user['role_id']);
    return $roleData ? true : false;
}

// Check if user is admin
function isAdmin() {
    return hasRole('Admin');
}

// Check if user is seller
function isSeller() {
    return hasRole('Seller');
}

// Check if user is moderator
function isModerator() {
    return hasRole('Moderator');
}

// Login user
function login($email, $password) {
    $user = getRow("SELECT * FROM users WHERE email = '" . escapeString($email) . "' AND status = 'active'");
    
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['role_id'];
        return true;
    }
    return false;
}

// Register user
function registerUser($username, $email, $password, $firstName = '', $lastName = '', $roleId = 4) {
    // Normalize role
    $validRole = getRow("SELECT id FROM roles WHERE id = " . intval($roleId));
    $roleId = $validRole ? intval($validRole['id']) : 4;

    // Check if user already exists
    if (getRow("SELECT id FROM users WHERE email = '" . escapeString($email) . "'")) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    if (getRow("SELECT id FROM users WHERE username = '" . escapeString($username) . "'")) {
        return ['success' => false, 'message' => 'Username already taken'];
    }
    
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    $data = [
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'role_id' => $roleId
    ];
    
    $userId = insert('users', $data);
    
    if ($userId) {
        return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
    }
    
    return ['success' => false, 'message' => 'Registration failed'];
}

// Logout user
function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('HTTP/1.0 403 Forbidden');
        echo '<h1>Access Denied</h1>';
        exit;
    }
}

// Redirect if not seller
function requireSeller() {
    requireLogin();
    if (!isSeller() && !isAdmin()) {
        header('HTTP/1.0 403 Forbidden');
        echo '<h1>Access Denied</h1>';
        exit;
    }
}
?>
