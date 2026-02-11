<?php
include 'login_base.php';  
$token = $_GET['token'] ?? '';
$_err = [];  // To store error messages for validation

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // Validate password
    if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Password must be between 5 and 100 characters!';
    } elseif ($password !== $confirm) {
        $_err['password'] = 'Passwords do not match!';
    } elseif ((!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/',$password))) {
        $_err['password'] = 'Password must include at least one uppercase letter, one number, and a special character (e.g., !, @, #, $, etc.).';
    } elseif (preg_match('/(password|123456|qwerty)/i', $password)) {
        $_err['password'] = 'Password is too weak. Please choose a stronger password.';
    }

    // Check if there are validation errors
    if (empty($_err)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if the token is valid and not expired
        $sql = "SELECT * FROM user WHERE reset_token_hash = ? AND reset_token_expires_at > NOW()";
        $stmt = $_db->prepare($sql);
        $stmt->execute([hash('sha256', $token)]);
        $user = $stmt->fetch();

        if ($user) {
            // Update the password and clear the token
            $sql = "UPDATE user SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$hashedPassword, $user->id]);

            temp('info', 'Password changed successfully.');
            header("Location: login.php");
            exit;
        } else {
            $_err['token'] = 'Invalid or expired token!';
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
  <title>Password Reset</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<div class="container">
    <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
        <label for="password">New Password</label>
        <input type="password" name="password" required>

        <label for="confirm">Confirm Password</label>
        <input type="password" name="confirm" required>

        <button type="submit">Submit</button>

        <!-- This is where errors will be displayed -->
        <?php if (!empty($_err)) : ?>
        <div class="error-messages">
          <?php foreach ($_err as $error) : ?>
            <p class="error"><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </form>

    <div class="back-link">
      <a href="reset_request.php"><i class="fa fa-long-arrow-left" aria-hidden="true"> Back</i></a>
    </div>
  </div>


</body>
</html>