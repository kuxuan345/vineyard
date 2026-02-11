<?php
session_start();
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alcohol_store";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user']['id'];

// Handle reward claiming
if (isset($_POST['claim_reward'])) {
    $rewardId = $_POST['reward_id'];
    $updateQuery = "UPDATE user_rewards SET is_claimed = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $rewardId, $userId);
    $stmt->execute();
}

// Fetch user's rewards
$query = "SELECT * FROM user_rewards WHERE user_id = ? ORDER BY reward_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

include('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gift</title>
    <link rel="stylesheet" href="css/myreward.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/menu.css">

</head>
<body>
    <div class="rewards-container">
    <a href="javascript:void(0);" class="back-btn" title="Back" onclick="window.history.back();">
        <i class="fa fa-arrow-left"></i>
    </a>
    
    <h1>My Rewards</h1>
    <a href="slotmachine.php" style="font-size: 15px; margin-top: -15px; margin-bottom: 15px">Slot Machine</a>


        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="reward-card">
                    <div class="reward-info">
                        <h3><?php echo htmlspecialchars($row['reward_name']); ?></h3>
                        <p>Won on: <?php echo date('F j, Y, g:i a', strtotime($row['reward_date'])); ?></p>
                    </div>
                    <?php if (!$row['is_claimed']): ?>
                        <form method="post">
                            <input type="hidden" name="reward_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="claim_reward" class="claim-button">Claim Reward</button>
                        </form>
                    <?php else: ?>
                        <button class="claim-button claimed" disabled>Claimed</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't won any rewards yet. Try your luck at the slot machine!</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>