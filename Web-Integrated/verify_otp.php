<?php
include 'login_base.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_otp = trim($_POST['otp'] ?? '');

    // Validate OTP format
    if ($input_otp === '') {
        $errors['otp'] = 'OTP is required.';
    } elseif (!preg_match('/^\d{6}$/', $input_otp)) {
        $errors['otp'] = 'Invalid OTP format. Please enter a 6-digit number.';
    } else {
        $user = $_SESSION['user_temp'] ?? null;

        if ($user) {
            $stmt = $_db->prepare('SELECT reset_token_hash, reset_token_expires_at FROM user WHERE id = ?');
            $stmt->execute([$user['id']]);
            $userToken = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userToken) {
                $storedOtp = str_replace('OTP:', '', $userToken['reset_token_hash']);
                $otpExpiresAt = $userToken['reset_token_expires_at'];

                if ($storedOtp === $input_otp && strtotime($otpExpiresAt) > time()) {
                    // OTP is valid
                    $_SESSION['user'] = $user; // Finalize login
                    unset($_SESSION['user_temp']); // Clean up temp session data

                    // Clear OTP fields
                    $updateStmt = $_db->prepare('UPDATE user SET reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?');
                    $updateStmt->execute([$user['id']]);

                    temp('info', 'Login successful.');
                    header('Location: menu.php');

                    exit();
                } else {
                    $errors['otp'] = 'Invalid or expired OTP. Please try again.';
                }
            } else {
                $errors['otp'] = 'Failed to verify OTP. Please try again.';
            }
        } else {
            $errors['otp'] = 'Session expired. Please log in again.';
        }
    }
}
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
  <link rel="stylesheet" href="css/resetpass.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/background.css">
  <title>Register Page</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<section class="reset-password-section">
    <div class="container">
        <form method="post" id="otpForm">
            <h2>Verify OTP</h2>
            <h3>OTP code is already sent to your account.</h3>
            <div class="input-box">
                <span class="icon"><i class="fa fa-key" aria-hidden="true"></i></span>
                <input type="text" name="otp" id="otp" required pattern="\d{6}" title="Enter a 6-digit OTP" placeholder="Enter OTP">
                <span class="error"><?= htmlspecialchars($errors['otp'] ?? '') ?></span>
            </div>
            <button type="submit">Verify</button>
        </form>
    </div>
</section>
<script>
    document.getElementById('otpForm').addEventListener('submit', function (e) {
        const otpInput = document.getElementById('otp').value;
        const otpPattern = /^\d{6}$/; // Matches a 6-digit number

        if (!otpPattern.test(otpInput)) {
            e.preventDefault();
            alert('Please enter a valid 6-digit OTP.');
        }
    });
</script>