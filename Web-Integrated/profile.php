<?php
$_css = 'css/profile.css';
include 'login_base.php';

// Ensure the user is logged in
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Get the logged-in user's ID
$userId = $_SESSION['user']['id'];
$name = $_SESSION['user']['name'];

// Fetch profile data for the logged-in user
$stmt = $_db->prepare('SELECT * FROM profile WHERE userID = ? AND name = ?');
$stmt->execute([$userId, $name]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_account'])) {
        // Handle delete account logic here
        $stmtDelete = $_db->prepare('DELETE FROM user WHERE id = ?');
        $stmtDelete->execute([$userId]);
        // You may want to log the user out after deletion
        session_destroy();
        header('Location: login.php');
        temp('info', 'Account is already deleted.');
        exit();
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
  <link rel="stylesheet" href="css/profile.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/background.css">
  <title>Login Page</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<section>

<div class="profile-container">
    <?php if ($profile): ?>
        <div class="profile-card">
            <div class="profile-photo">
                <img src="user_photos/<?= htmlspecialchars($profile['photo'] ?? 'default.jpg') ?>" alt="User Photo">
            </div>
            <div class="profile-details">
                <h1><i class="fa fa-user"></i> <?= htmlspecialchars($profile['name'] ?? 'Name Not Available') ?></h1>
                <table>
                    <tr>
                        <td><strong>Age:</strong></td>
                        <td><?= htmlspecialchars($profile['age'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Birth:</strong></td>
                        <td><?= htmlspecialchars($profile['birth'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Contact:</strong></td>
                        <td>+60 <?= htmlspecialchars($profile['contact_number'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td><?= htmlspecialchars($profile['address'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>State:</strong></td>
                        <td><?= htmlspecialchars($profile['state'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>City:</strong></td>
                        <td><?= htmlspecialchars($profile['city'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Postal Code:</strong></td>
                        <td><?= htmlspecialchars($profile['postal_code'] ?? 'N/A') ?></td>
                    </tr>
                </table>
                
                <div class="button-container">
                <form method="POST" action="edit_profile.php">
                    <button type="submit" name="edit_profile" data-tooltip="Edit Profile">
                        <i class="fa fa-edit" style="font-size:24px"></i>
                    </button>
                </form>

                <form action="password.php">
                    <button type="submit" name="reset_password" data-tooltip="Reset Password">
                        <i class='fas fa-key' style='font-size:24px'></i>
                    </button>
                </form>

                <form method="POST">
                    <button type="submit" name="delete_account" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');" data-tooltip="Delete Account">
                        <i class="fa fa-trash-o" style="font-size:24px"></i>
                    </button>
                </form>

                <form action="logout.php">
                    <button type="submit" name="logout" data-tooltip="Log Out">
                        <i class="fa fa-sign-out" style="font-size:24px"></i>
                    </button>
                </form>

                <form action="menu.php">
                    <button type="submit" name="home" data-tooltip="Go Home">
                        <i class="fa fa-home" style="font-size:24px"></i>
                    </button>
                </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
</section>
</body>
</html>
