<?php
include "config.php";
session_start();

$error = "";

if (isset($_POST['login'])) {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $sql = "SELECT * FROM users 
            WHERE email = ? AND is_verified = 1 
            ORDER BY id DESC 
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            header("Location: display.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not registered or not verified!";
    }
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            padding: 40px 35px;
            border-radius: 15px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .login-card h3 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .login-card p {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
        }

        .form-control {
            height: 48px;
            border-radius: 8px;
            font-size: 15px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #6c63ff;
        }

        .btn-login {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            height: 48px;
            font-weight: 500;
            letter-spacing: 1px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .links {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .links a {
            text-decoration: none;
            color: #6c63ff;
            font-weight: 500;
        }

        .icon-box {
            text-align: center;
            font-size: 45px;
            color: #6c63ff;
            margin-bottom: 10px;
        }
    </style>

</head>
<body>

<div class="login-card">

    <div class="icon-box">
        <i class="fa fa-user-lock"></i>
    </div>

    <h3>Welcome Back</h3>
    <p>Please login to your account</p>

    <!-- Error Message -->
    <?php if ($error != "") { ?>
        <div class="alert alert-danger text-center">
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <form method="post">

        <!-- Email -->
        <input type="email"
               name="email"
               class="form-control mb-3"
               placeholder="Email Address"
               required>

        <!-- Password -->
        <input type="password"
               name="password"
               class="form-control mb-3"
               placeholder="Password"
               required>

        <!-- Button -->
        <button name="login" class="btn btn-login text-white w-100">
            Login
        </button>

    </form>

    <div class="links">
        <a href="forgot-password.php">Forgot password?</a>
        <br>
        New here?
        <a href="register.php">Create an account</a>
    </div>

</div>

</body>
</html>

