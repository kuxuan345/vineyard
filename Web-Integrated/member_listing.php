<?php
require 'login_base.php'; 


try {
    $stmt = $_db->query('SELECT * FROM profile');
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching profiles: " . $e->getMessage());
}


include('admin_header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Member Listing</title>
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="css/memberlist.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="member-list">
        <h1>Profile Details</h1>
        <p><?= count($profiles) ?> profile(s) found.</p>
        <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>  
                        <th>Age</th>
                        <th>Date of Birth</th>
                        <th>Contact Number</th>
                        <th >Address</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Postal Code</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php if ($profiles): ?>
                        <?php foreach ($profiles as $profile): ?>
                            <tr>
                                <td><?= htmlspecialchars($profile['userID']) ?></td>
                                <td><?= htmlspecialchars($profile['name']) ?></td>
                                <td><?= htmlspecialchars($profile['age']) ?></td>
                                <td><?= htmlspecialchars($profile['birth']) ?></td>
                                <td><?= htmlspecialchars($profile['contact_number']) ?></td>
                                <td><?= htmlspecialchars($profile['address']) ?></td>
                                <td><?= htmlspecialchars($profile['state']) ?></td>
                                <td><?= htmlspecialchars($profile['city']) ?></td>
                                <td><?= htmlspecialchars($profile['postal_code']) ?>
                                <img src="user_photos/<?= htmlspecialchars($profile['photo'] ?? 'default.jpg') ?>" alt="User Photo" class ="popup"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No profiles found.</td>
                        </tr>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>


