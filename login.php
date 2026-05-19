<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$registered = isset($_GET['registered']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } elseif (login($email, $password)) {
        // Check user role for redirect
        $user = getCurrentUser();
        if (isAdmin()) {
            header('Location: admin/dashboard.php');
        } elseif (isSeller()) {
            header('Location: dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NC Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-store"></i> NC Traders
            </a>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4 fw-bold">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </h2>

                        <?php if ($registered): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success!</strong> Registration successful. Please login.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Enter your email" required autofocus>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter your password" required>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="fas fa-unlock"></i> Login
                                </button>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                        </form>

                        <hr class="my-4">

                        <p class="text-center text-muted mb-3">
                            <a href="#" class="text-primary text-decoration-none fw-bold">Forgot Password?</a>
                        </p>

                        <p class="text-center text-muted mb-0">
                            Don't have an account? 
                            <a href="register.php" class="fw-bold text-primary">Register here</a>
                        </p>
                    </div>
                </div>

                <!-- Demo Credentials -->
                <div class="card mt-4 bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title fw-bold text-muted mb-3">
                            <i class="fas fa-info-circle"></i> Demo Credentials
                        </h6>
                        <small class="text-muted">
                            <p><strong>Email:</strong> demo@example.com</p>
                            <p><strong>Password:</strong> demo123</p>
                        </small>
                    </div>
                </div>
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
</body>
</html>
