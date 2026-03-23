<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

include "config.php";

if (isset($_POST['register'])) {

    // Clean inputs
    $name     = trim($_POST['name'] ?? '');
    $email    = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $gender   = trim($_POST['gender'] ?? '');
    $contact  = trim($_POST['contact'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        die("All fields are required!");
    }

    // Hash password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Generate OTP
    $otp = sprintf("%06d", rand(0, 999999));

    // Check email
    $check = mysqli_prepare($conn,
        "SELECT id FROM users WHERE email=? LIMIT 1"
    );

    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);

    if ($row = mysqli_fetch_assoc($result)) {

        // Update
        $update = mysqli_prepare($conn,
            "UPDATE users 
             SET otp=?, password=?, is_verified=0 
             WHERE id=?"
        );

        mysqli_stmt_bind_param($update, "ssi",
            $otp,
            $password,
            $row['id']
        );

        mysqli_stmt_execute($update);

    } else {

        // Insert
        $insert = mysqli_prepare($conn,
            "INSERT INTO users
            (name,email,password,gender,contact,otp,is_verified)
            VALUES(?,?,?,?,?,?,0)"
        );

        mysqli_stmt_bind_param($insert, "ssssss",
            $name,
            $email,
            $password,
            $gender,
            $contact,
            $otp
        );

        mysqli_stmt_execute($insert);
    }

    /* =====================
       SEND OTP EMAIL
    ====================== */

    try {

        $mail = new PHPMailer(true);

        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;

        // Your Gmail
        $mail->Username   = "aryannaithani88@gmail.com";

        // App Password (NOT normal password)
        $mail->Password   = "igpfhott ttldyxvf";

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender
        $mail->setFrom("aryannaithani88@gmail.com", "OTP Verification");

        // Receiver
        $mail->addAddress($email);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Code";

        $mail->Body = "
            <h2>Email Verification</h2>
            <p>Your OTP is:</p>
            <h3>$otp</h3>
            <p>This OTP is valid for 10 minutes.</p>
        ";

        $mail->send();

        // Redirect to verify page
        header("Location: verify.php?email=" . urlencode($email));
        exit();

    } catch (Exception $e) {

        echo "Mail Error: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

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
            justify-content: center;
            align-items: center;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 35px;
            border-radius: 15px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .register-card h2 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
        }

        .form-control {
            height: 48px;
            border-radius: 8px;
            padding-left: 45px;
            font-size: 15px;
        }

        .input-group-text {
            background: transparent;
            border-right: none;
            color: #6c63ff;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #6c63ff;
        }

        .btn-register {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            height: 48px;
            font-weight: 500;
            letter-spacing: 1px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-register:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .footer-text {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #666;
        }

        .footer-text a {
            color: #6c63ff;
            text-decoration: none;
            font-weight: 500;
        }
    </style>

</head>
<body>

<div class="register-card">

    <h2>Create Account</h2>

    <form method="post">

        <div class="input-group mb-3">
            <span class="input-group-text">
                <i class="fa fa-user"></i>
            </span>
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text">
                <i class="fa fa-envelope"></i>
            </span>
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text">
                <i class="fa fa-lock"></i>
            </span>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text">
                <i class="fa fa-venus-mars"></i>
            </span>
            <select name="gender" class="form-control" required>
                <option value="">Select Gender</option>
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
            </select>
        </div>

        <div class="input-group mb-4">
            <span class="input-group-text">
                <i class="fa fa-phone"></i>
            </span>
            <input type="text" name="contact" class="form-control" placeholder="Contact Number" required>
        </div>

        <button name="register" class="btn btn-register text-white w-100">
            Register Now
        </button>

    </form>

    <div class="footer-text">
        Already have an account?
        <a href="#">Login</a>
    </div>

</div>

</body>
</html>

