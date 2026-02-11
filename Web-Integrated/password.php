<?php
include 'login_base.php';

// Ensure the user is logged in
$_user = $_SESSION['user'] ?? null;
if (!$_user || !isset($_user['id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$_err = []; // Initialize error array

if (is_post()) {
    $password = trim(req('password'));
    $new_password = trim(req('new_password'));
    $confirm = trim(req('confirm'));

    // Validate current password
    if ($password === '') {
        $_err['password'] = 'Current password is required.';
    } else {
        $stm = $_db->prepare('SELECT password FROM user WHERE id = ?');
        $stm->execute([$_user['id']]);
        $stored_password = $stm->fetchColumn();

        if (!$stored_password || !password_verify($password, $stored_password)) {
            $_err['password'] = 'Current password is incorrect.';
        }
    }

    // Validate new password
    if ($new_password === '') {
        $_err['new_password'] = 'New password is required.';
    } elseif (strlen($new_password) < 5 || strlen($new_password) > 100) {
        $_err['new_password'] = 'New password must be between 5-100 characters.';
    } elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $_err['new_password'] = 'New password must include at least one uppercase letter and one number.';
    }

    // Validate confirm password
    if ($confirm === '') {
        $_err['confirm'] = 'Confirmation password is required.';
    } elseif ($confirm !== $new_password) {
        $_err['confirm'] = 'Passwords do not match.';
    }

    // Update password in the database
    if (empty($_err)) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stm = $_db->prepare('UPDATE user SET password = ? WHERE id = ?');
        $stm->execute([$hashed_new_password, $_user['id']]);

        temp('info', 'Password updated successfully.');
        redirect('/');
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
  <link rel="stylesheet" href="css/profilepass.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/background.css">
  <title>Login Page</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<section>
<div class="container">
    <form method="post" class="form">
    <h2>Reset Password</h2>
        <div class="input-box">
            <input type="password" name="password" required placeholder="Current Password">
            <span class="error"><?= htmlspecialchars($_err['password'] ?? '') ?></span>
        </div>

        <div class="input-box">
            <input type="password" name="new_password" required placeholder="New Password">
            <span class="error"><?= htmlspecialchars($_err['new_password'] ?? '') ?></span>
        </div>

        <div class="input-box">
            <input type="password" name="confirm" required placeholder="Confirm Password">
            <span class="error"><?= htmlspecialchars($_err['confirm'] ?? '') ?></span>
        </div>

        <button type="submit">Submit</button>
        <button type="reset">Reset</button>
        <div class="back-link">
            <a href="profile.php"><i class="fa fa-long-arrow-left" aria-hidden="true"> Back</i></a>
        </div>
    </form>
    </div>
</section>
</body>
</html>
