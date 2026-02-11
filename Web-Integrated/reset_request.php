<?php
include 'login_base.php';  

$message = ''; // Variable to hold success or error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if the email is valid and exists in the database
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token);  // Store the hashed token
        $expiry = date("Y-m-d H:i:s", time() + 3600); // Token expires in 1 hour

        // Store token and expiry in the database
        $sql = "UPDATE user SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$token_hash, $expiry, $email]);

        // Send reset email
        $reset_link = "http://localhost:8000/reset_password.php?token=$token";

        // Use get_mail() to get the PHPMailer instance and send the email
        $mail = get_mail(); // Get the PHPMailer object from _base.php

        try {
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request'; // The subject is here
            $mail->Body = "Click <a href='$reset_link'>here</a> to reset your password.";

            $mail->send();
            $message = "Password reset link sent to your email!";
        } catch (Exception $e) {
            $message = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $message = "No user found with that email address!";
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
<body>
<div class="container">
    <a href="login.php" class="back-btn" title="Back">
        <i class="fa fa-arrow-left"></i>
    </a>
    <h2>Password Reset Request</h2>  
    <form method="POST" action="reset_request.php">
        <label for="email">Enter your email address:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Submit</button>
    </form> 
    <?php if ($message): ?>
        <p><?php echo $message; ?></p> 
    <?php endif; ?>
</div>
</body>
</html>