<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

$error = '';
$success = '';
$username = '';
$email = '';
$firstName = '';
$lastName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $firstName = sanitize($_POST['first_name'] ?? '');
    $lastName = sanitize($_POST['last_name'] ?? '');

$phone = sanitize($_POST['phone'] ?? '');

$roleId = 4;

    // Validation
    if (
empty($username)
|| empty($email)
|| empty($phone)
|| empty($password)
|| empty($confirmPassword)
) {
        $error = 'All fields are required';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $result = registerUser(
$username,
$email,
$password,
$firstName,
$lastName,
$phone,
);
        if ($result['success']) {
            $success = 'Registration successful! Please log in.';
            header('Location: login.php?registered=true');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NC Traders</title>
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

    <!-- Registration Form -->
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4 fw-bold">
                            <i class="fas fa-user-plus"></i> Create Account
                        </h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="first_name" placeholder="John" value="<?php echo htmlspecialchars($firstName); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Doe" value="<?php echo htmlspecialchars($lastName); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="johndoe" required value="<?php echo htmlspecialchars($username); ?>">
                                <small class="text-muted">Must be unique</small>
                            </div>
                            <div class="mb-3">

<label for="phone"
class="form-label">

Phone Number
<span class="text-danger">*</span>

</label>

<input type="text"
class="form-control"
id="phone"
name="phone"
placeholder="0841234567"
required>

</div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" required value="<?php echo htmlspecialchars($email); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="At least 6 characters" required>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Re-enter password" required>
                            </div>
<div class="form-check mb-4">

<input class="form-check-input"
type="checkbox"
name="terms"
id="terms"
required>

<label class="form-check-label"
for="terms">

I agree to the

<a href="terms.php">

Terms & Conditions

</a>

and

<a href="privacy-policy.php">

Privacy Policy

</a>

</label>

</div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                                <i class="fas fa-user-check"></i> Register
                            </button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center text-muted mb-0">
                            Already have an account? 
                            <a href="login.php" class="fw-bold text-primary">Login here</a>
                        </p>
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
