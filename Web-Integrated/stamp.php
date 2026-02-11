<?php
session_start();
try {
    $conn = new mysqli("localhost", "root", "", "alcohol_store");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_SESSION['user']['id'])) {
        header('Location: login.php');
        exit();
    }

    $userId = $_SESSION['user']['id'];

    // Fetch user's available stamps
    $stmtStamps = $conn->prepare('SELECT stamps_available FROM user_stamps WHERE user_id = ?');
    $stmtStamps->bind_param("i", $userId);
    $stmtStamps->execute();
    $result = $stmtStamps->get_result();
    $userStamps = $result->fetch_assoc();
    $availableStamps = $userStamps ? $userStamps['stamps_available'] : 0;

    // Get total used stamps
    $stmtUsedStamps = $conn->prepare('
        SELECT COUNT(*) as used_stamps 
        FROM stamp_transactions 
        WHERE user_id = ? AND transaction_type = "used"
    ');
    $stmtUsedStamps->bind_param("i", $userId);
    $stmtUsedStamps->execute();
    $result = $stmtUsedStamps->get_result();
    $usedStamps = $result->fetch_assoc()['used_stamps'];

    // Add this to pass data to JavaScript
    $stampData = [
        'available' => $availableStamps,
        'used' => $usedStamps
    ];

} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    die("Error connecting to database");
}

include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reward System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/stamp.css">
  <link rel="stylesheet" href="css/background.css">
  <link rel="stylesheet" href="css/menu.css">
</head>
<body>
  <div class="reward-system">
    <h2>THE HIPZ STAMP COLLECTOR</h2>
    <p>Each time you make a purchase, you'll earn a stamp.</p>
    <p>[ 1 Bottle = 1 Stamp ]</p>
    
    <p>Available stamps: <?= htmlspecialchars($availableStamps) ?></p>
   
    <div class="stamp-container">
      <div class="stamp" id="stamp1"><i class="far fa-snowflake" style='font-size:70px'></i></div>
      <div class="stamp" id="stamp2"><i class="far fa-snowflake" style='font-size:70px'></i></div>
      <div class="stamp" id="stamp3"><i class="far fa-snowflake" style='font-size:70px'></i></div>
      <div class="stamp" id="stamp4"><i class="far fa-snowflake" style='font-size:70px'></i></div>
      <div class="stamp" id="stamp5"><i class="fas fa-gift" style='font-size:70px'></i></div>
    </div>
   
    <button onclick="addStamp()">Stamp</button>
    <p id="status"></p>
  </div>

  <!-- Add this before your script include -->
  <script>
    const stampData = <?= json_encode($stampData) ?>;
  </script>
  <script src="js/stamp.js"></script>
</body>
</html>