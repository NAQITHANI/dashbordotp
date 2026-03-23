<?php
include "config.php";
session_start();

$error = "";

if (isset($_GET['email'])) {
    $_SESSION['verify_email'] = strtolower(trim($_GET['email']));
}

$email = $_SESSION['verify_email'] ?? '';

if (isset($_POST['verify'])) {

    $otp = trim($_POST['otp']);

    // Debug (remove later)
    // echo $email." | ".$otp; exit;

    if ($email == "") {
        $error = "Session expired. Register again.";
    } else {

        $sql = "SELECT id, otp FROM users 
                WHERE email=? AND is_verified=0 
                LIMIT 1";

        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt,"s",$email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($res)) {

            if ($otp === $row['otp']) {

                $update = mysqli_prepare($conn,
                 "UPDATE users 
                  SET is_verified=1, otp=NULL 
                  WHERE id=?"
                );

                mysqli_stmt_bind_param($update,"i",$row['id']);
                mysqli_stmt_execute($update);

                unset($_SESSION['verify_email']);

                header("Location: login.php");
                exit;

            } else {
                $error = "Invalid OTP!";
            }

        } else {
            $error = "Account not found or already verified!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>

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

        .otp-card {
            background: rgba(255, 255, 255, 0.96);
            padding: 35px 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .otp-card h3 {
            text-align: center;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .otp-card p {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
        }

        .otp-input {
            height: 50px;
            font-size: 18px;
            letter-spacing: 4px;
            text-align: center;
            border-radius: 8px;
        }

        .otp-input:focus {
            box-shadow: none;
            border-color: #6c63ff;
        }

        .btn-verify {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            height: 48px;
            font-weight: 500;
            border-radius: 8px;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-verify:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .icon-box {
            text-align: center;
            font-size: 45px;
            color: #6c63ff;
            margin-bottom: 10px;
        }

        .resend {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .resend a {
            text-decoration: none;
            font-weight: 500;
            color: #6c63ff;
        }
    </style>

</head>
<body>

<div class="otp-card">

    <div class="icon-box">
        <i class="fa fa-shield-alt"></i>
    </div>

    <h3>OTP Verification</h3>

    <p>Please enter the 6-digit code sent to your email</p>

    <?php if($error!=""){ ?>
        <div class="alert alert-danger text-center">
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <form method="post">

        <input type="text"
               name="otp"
               class="form-control otp-input mb-3"
               placeholder="------"
               maxlength="6"
               required>

        <button name="verify" class="btn btn-verify text-white w-100">
            Verify & Continue
        </button>

    </form>
    <div class="resend">
        Didn’t receive code?
        <a href="resend.php">Resend OTP</a>
    </div>

</div>

</body>
</html>

