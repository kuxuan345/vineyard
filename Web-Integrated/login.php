<?php 
include 'login_base.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate email
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    // Validate password
    if ($password === '') {
        $errors['password'] = 'New password is required.';
    } elseif (strlen($password) < 5 || strlen($password) > 100) {
        $errors['password'] = 'Password must be between 5-100 characters.';
    } elseif ((!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/',$password))) {
        $_err['password'] = 'Password must include at least one uppercase letter, one number and special character (e.g., !, @, #, $, etc.).';
    }

    // If no errors, proceed with login
    if (!$errors) {
        $stmt = $_db->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->execute([$email]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array
        
        if ($user && password_verify($password, $user['password'])) {
            // Password matches, proceed with login
            if ($user['role'] === 'Admin') {
                header('Location: admin.php');
            }else{
                $otp = rand(100000, 999999); // 6-digit OTP
            $otpHash = 'OTP:' . $otp; // Prefix for differentiation
            $otpExpiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes')); 

            // Store OTP in the reset_token fields
            $updateStmt = $_db->prepare('UPDATE user SET reset_token_hash = ?, reset_token_expires_at = ? WHERE id = ?');
            $updateStmt->execute([$otpHash, $otpExpiresAt, $user['id']]);

            // Send OTP using PHPMailer
            try {
                $mail = get_mail(); // Use the function from _base.php
                $mail->addAddress($email, $user['name']); // Add recipient
                $mail->Subject = "Your OTP Code";
                $mail->Body = "Hello, your OTP code is <strong>$otp</strong>. It is valid for 5 minutes.";
                $mail->isHTML(true); // Enable HTML formatting

                $mail->send();

                $_SESSION['user_temp'] = $user; // Temporarily store user data
                header('Location: verify_otp.php'); // Redirect to OTP verification
                exit();
            } catch (Exception $e) {
                $errors['email'] = "Failed to send OTP. Mailer Error: " . $mail->ErrorInfo;
            }
            } 
            
            
        } else {
            $errors['password'] = 'Invalid email or password.';
        }
    }
}

$_title = 'Login';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/background.css">
  <title>Login Page</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<section>
<div class="login-box">
    <form method="post">
        <h2>Login</h2>
        <div class="input-box">
            <span class="icon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <label>Email</label>
            <span class="error"><?= htmlspecialchars($errors['email'] ?? '') ?></span>
        </div>
        <div class="input-box">
            <span class="icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
            <input type="password" name="password" required>
            <label>Password</label>
            <span class="error"><?= htmlspecialchars($errors['password'] ?? '') ?></span>
        </div>
        <div class="remember-forget">
            <label><input type="checkbox" name="remember"> Remember Me</label>
            <a href="reset_request.php">Forgot Password?</a>
        </div>
        <button type="submit">Login</button>
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register Now</a></p>
        </div>
        <div class="back-link">
            <a href="menu.php"><i class="fa fa-long-arrow-left" aria-hidden="true"> Back</i></a>
        </div>
    </form>
</div>
</section>
</body>
</html>
