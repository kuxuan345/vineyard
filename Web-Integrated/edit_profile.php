<?php
require 'login_base.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];
$stmt = $_db->prepare('SELECT * FROM profile WHERE userID = ?');
$stmt->execute([$userId]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$_err = [];
$uploadedPhoto = $profile['photo'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $birth = trim($_POST['birth'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmpPath = $_FILES['photo']['tmp_name'];
        $photoName = basename($_FILES['photo']['name']);
        $photoExt = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($photoExt, $allowedExtensions)) {
            $newPhotoName = uniqid('photo_', true) . '.' . $photoExt;
            $photoUploadDir = __DIR__ . '/user_photos/';
            $photoUploadPath = $photoUploadDir . $newPhotoName;
            if (!is_dir($photoUploadDir)) {
                mkdir($photoUploadDir, 0777, true);
            }
            if (move_uploaded_file($photoTmpPath, $photoUploadPath)) {
                $uploadedPhoto = $newPhotoName;
            } else {
                $_err['photo'] = 'Failed to upload the photo.';
            }
        } else {
            $_err['photo'] = 'Invalid photo format.';
        }
    }

    if (!$name) $_err['name'] = 'Name is required.';
    if (!$contact_number) $_err['contact_number'] = 'Contact number is required.';
    if (!$address) $_err['address'] = 'Address is required.';
    if (!$state) $_err['state'] = 'State is required.';
    if (!$city) $_err['city'] = 'City is required.';
    if (!$postal_code) $_err['postal_code'] = 'Postal code is required.';

    if (empty($_err)) {
        try {
            var_dump([$name, $contact_number, $address, $state, $city, $postal_code, $uploadedPhoto, $age, $birth, $userId]);
            $stmt = $_db->prepare('
                UPDATE profile
                SET name = ?, contact_number = ?, address = ?, state = ?, city = ?, postal_code = ?, photo = ?, age = ?, birth = ?
                WHERE userID = ?
            ');
            $stmt->execute([$name, $contact_number, $address, $state, $city, $postal_code, $uploadedPhoto, $age, $birth, $userId]);
            temp('info', 'Profile updated successfully.');
            redirect('profile.php');
        } catch (Exception $e) {
            temp('info', 'Got some error cannot update.');
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
  <link rel="stylesheet" href="css/editprofile.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/background.css">
  <title>Login Page</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<section>
<div class="edit-profile-container">
    <?php if (!empty($_err['general'])): ?>
        <div class="error-message"><?= htmlspecialchars($_err['general']) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
    <h1>Edit Profile</h1>
    <div class="form-group">
        <label>Photo:</label>
        <input type="file" name="photo">
        <?= !empty($_err['photo']) ? '<p class="error">'.htmlspecialchars($_err['photo']).'</p>' : '' ?>
    </div>
    <div class="form-group">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $profile['name'] ?? '') ?>">
        <?= !empty($_err['name']) ? '<p class="error">'.htmlspecialchars($_err['name']).'</p>' : '' ?>
    </div>
    <div class="form-group">
        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($_POST['contact_number'] ?? $profile['contact_number'] ?? '') ?>">
        <?= !empty($_err['contact_number']) ? '<p class="error">'.htmlspecialchars($_err['contact_number']).'</p>' : '' ?>
    </div>
    <div class="form-group">
        <label>Age:</label>
        <input type="number" name="age" value="<?= htmlspecialchars($_POST['age'] ?? $profile['age'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Birth date:</label>
        <input type="date" name="birth" value="<?= htmlspecialchars($_POST['birth'] ?? $profile['birth'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($_POST['address'] ?? $profile['address'] ?? '') ?>">
        <?= !empty($_err['address']) ? '<p class="error">'.htmlspecialchars($_err['address']).'</p>' : '' ?>
    </div>
    <div class="form-group">
        <label>State:</label>
        <input type="text" name="state" value="<?= htmlspecialchars($_POST['state'] ?? $profile['state'] ?? '') ?>">
        <?= !empty($_err['state']) ? '<p class="error">'.htmlspecialchars($_err['state']).'</p>' : '' ?>
    </div>
    <div class="form-group">
        <label>City:</label>
        <input type="text" name="city" value="<?= htmlspecialchars($_POST['city'] ?? $profile['city'] ?? '') ?>">
        <?= !empty($_err['city']) ? '<p class="error">'.htmlspecialchars($_err['city']).'</p>' : '' ?>
    </div>
    <div class="form-group">
        <label>Postal Code:</label>
        <input type="text" name="postal_code" value="<?= htmlspecialchars($_POST['postal_code'] ?? $profile['postal_code'] ?? '') ?>">
        <?= !empty($_err['postal_code']) ? '<p class="error">'.htmlspecialchars($_err['postal_code']).'</p>' : '' ?>
    </div>
    <div class="form-actions">
        <button type="submit">Save</button>
        <button type="button" onclick="window.location.href='profile.php'">Cancel</button>
    </div>
</form>
</div>
</section>
</body>
</html>