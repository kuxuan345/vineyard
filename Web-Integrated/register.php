<?php
include 'login_base.php';

if (is_post()) {
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $name     = req('name');

    // Validation
    $_err = [];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = 'Invalid email format.';
    } else {
        // Check if email already exists in the database
        $stmEmailCheck = $_db->prepare('SELECT COUNT(*) FROM user WHERE email = ?');
        $stmEmailCheck->execute([$email]);
        if ($stmEmailCheck->fetchColumn() > 0) {
            $_err['email'] = 'This email is already registered.';
        }
    }

    // Validate password
    if ($password === '') {
        $_err['password'] = 'Password is required.';
    } elseif (strlen($password) < 8 || strlen($password) > 20) {
        $_err['password'] = 'Password must be between 8-20 characters.';
    } elseif ((!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/',$password))) {
        $_err['password'] = 'Password must include at least one uppercase letter, one number and special character (e.g., !, @, #, $, etc.).';
    } elseif (preg_match('/(password|123456|qwerty)/i', $password)) {
        $_err['password'] = 'Password is too weak. Please choose a stronger password.';
    }

    // Validate username
    if (strlen($name) < 3) {
        $_err['name'] = 'Username must be at least 3 characters long.';
    } else {
        // Check if username already exists
        $stmUsernameCheck = $_db->prepare('SELECT COUNT(*) FROM user WHERE name = ?');
        $stmUsernameCheck->execute([$name]);
        if ($stmUsernameCheck->fetchColumn() > 0) {
            $_err['name'] = 'This username is already taken.';
        }
    }

    if (!$_err) {
        $_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $_db->beginTransaction();

        try {
            // Insert into `user` table
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmUser = $_db->prepare('
                INSERT INTO user (email, password, name, role)
                VALUES (?, ?, ?, "Member")
            ');
            $stmUser->execute([$email, $hashedPassword, $name]);
            $userId = $_db->lastInsertId(); // Get the new user's ID
            $register_time = date("Y-m-d H:i:s");
            // Insert into `profile` table
            $stmProfile = $_db->prepare('
                INSERT INTO profile (name, userID,register_time)
                VALUES (?, ?, ?)
            ');
            $stmProfile->execute([$name, $userId, $register_time]);

            $_db->commit();

            temp('info', 'Registration successful.');
            redirect('/login.php');
        } catch (Exception $e) {
            $_db->rollBack();
            error_log("Error: " . $e->getMessage());
            $_err['general'] = 'An error occurred while registering. Please try again.';
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
  <link rel="stylesheet" href="css/register.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/background.css">
  <title>Register Page</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<section>
    <div class="register-box">
        <form action="register.php" method="post" enctype="multipart/form-data">
            <h2>Register</h2>

            <!-- Email -->
            <div class="input-box">
                <span class="icon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                <input type="email" name="email" value="<?= encode($email ?? '') ?>" required>
                <label>Email</label>
                <?php err('email'); ?> 
            </div>

            <!-- Username -->
            <div class="input-box">
                <span class="icon"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                <input type="text" name="name" value="<?= encode($name ?? '') ?>" required>
                <label>Username</label>
                <?php err('name'); ?>  
            </div>

            <!-- Password -->
            <div class="input-box">
                <span class="icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                <input type="password" name="password" required>
                <label>Password</label>
                <?php err('password'); ?> 
            </div>

            <!-- Confirm Password -->
            <div class="input-box">
                <span class="icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                <input type="password" name="confirm" required>
                <label>Confirm Password</label>
                <?php err('confirm'); ?> 
            </div>

            <!-- Submit Button -->
            <button type="submit">Register</button>
        </form>

        <!-- Back Link -->
        <div class="back-link">
            <a href="login.php"><i class="fa fa-long-arrow-left" aria-hidden="true"> Back</i></a>
        </div>
    </div>
</section>
</body>
</html>

