<?php
session_start();
session_regenerate_id(true);

include 'config/database.php';

$message = "";

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

if(password_verify($password, $user['password'])){

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    $_SESSION['fullname'] =
    $user['first_name'] . " " . $user['last_name'];

    if($user['role'] == 'admin'){

        $_SESSION['admin_id'] = $user['id'];

        $_SESSION['admin_name'] =
        $user['first_name'] . " " . $user['last_name'];

        header("Location: admin/admin-dashboard.php");
        exit();

    }else{

        header("Location: dashboard.php");
        exit();

    }

} else {

    $message = "Incorrect Password!";
}

    } else {

        $message = "Account does not exist!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | NCTraders</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-body p-5">

                    <h2 class="text-center mb-4">
                        Login
                    </h2>

                    <?php if($message != ""){ ?>
                        <div class="alert alert-danger">
                            <?php echo $message; ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label>Email</label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="mb-3">

                            <label>Password</label>

                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   required>

                        </div>
<button type="submit"
        name="login"
        class="btn btn-dark w-100">

    Login

</button>

</form>

<div class="mt-3 text-center">

<a href="forgot-password.php">

Forgot Password?

</a>

</div>

<div class="text-center mt-3">

    <a href="register.php">
        Create Account
    </a>

</div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>


