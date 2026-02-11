<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alcohol_store";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];

// Get user's spin information
$spinQuery = "SELECT COUNT(*) as total_transactions FROM checkout WHERE user_id = ?";
$stmt = $conn->prepare($spinQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$transactionResult = $stmt->get_result();
$transactionData = $transactionResult->fetch_assoc();
$totalTransactions = $transactionData['total_transactions'];

// Get used spins
$usedSpinsQuery = "SELECT spins_used FROM user_spins WHERE user_id = ?";
$stmt = $conn->prepare($usedSpinsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$spinsResult = $stmt->get_result();
$spinsData = $spinsResult->fetch_assoc();
$spinsUsed = $spinsData ? $spinsData['spins_used'] : 0;

// Calculate available spins
$availableSpins = $totalTransactions - $spinsUsed;

// Fetch slot machine rewards from the database
$sql = "SELECT * FROM slot_rewards";
$result = $conn->query($sql);

$rewards = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rewards[$row['reward_name']] = $row['image'];
    }
}

include('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slot Machine Game</title>
    <link rel="stylesheet" href="css/spin.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <div class="slot-machine">
        <h2>THE HIPZ SLOT MACHINE GAME</h2>
        <p>[ 1 Token = 1 Spin ]</p>
        <p>Available Spins: <?php echo $availableSpins; ?></p>
        <div class="slots">
            <div class="slot" id="slot1"></div>
            <div class="slot" id="slot2"></div>
            <div class="slot" id="slot3"></div>
            <div class="slot" id="slot4"></div>
            <div class="slot" id="slot5"></div>
        </div>
        <button id="spin-button" <?php echo $availableSpins <= 0 ? 'disabled' : ''; ?>>Spin</button>
        <p id="reward-message"><?php echo $availableSpins <= 0 ? 'No spins available. Make a purchase to earn spins!' : 'Press "Spin" to test your luck!'; ?></p>
    </div>

    <!-- Passing data to JavaScript -->
    <script>
        const rewards = <?php echo json_encode(array_values($rewards)); ?>;
        const rewardNames = <?php echo json_encode(array_keys($rewards)); ?>;
        const availableSpins = <?php echo $availableSpins; ?>;
        const userId = <?php echo $userId; ?>;
    </script>
    <script src="js/spin.js"></script>
</body>
</html>

<?php $conn->close(); ?>