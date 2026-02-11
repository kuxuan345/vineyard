<?php
// save_reward.php
session_start();
if (!isset($_SESSION['user']['id']) || !isset($_POST['reward'])) {
    die(json_encode(['error' => 'Invalid request']));
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alcohol_store";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

$userId = $_SESSION['user']['id'];
$reward = $_POST['reward'];

// Get the last spin ID
$spinQuery = "SELECT id FROM user_spins WHERE user_id = ? ORDER BY last_updated DESC LIMIT 1";
$stmt = $conn->prepare($spinQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$spinResult = $stmt->get_result();
$spinData = $spinResult->fetch_assoc();
$spinId = $spinData['id'];

// Only save reward if it's not "spin again"
$spin_again = false;
if ($reward === "Hmmmm! Please Spin Again") {
    $spin_again = true;
    // Update the spins_used count to give back the spin
    $updateSpinsQuery = "UPDATE user_spins SET spins_used = spins_used - 1 WHERE id = ?";
    $stmt = $conn->prepare($updateSpinsQuery);
    $stmt->bind_param("i", $spinId);
    $stmt->execute();
} else {
    // Save the reward
    $saveQuery = "INSERT INTO user_rewards (user_id, reward_name, spin_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($saveQuery);
    $stmt->bind_param("isi", $userId, $reward, $spinId);
    $stmt->execute();
}

echo json_encode(['success' => true, 'spin_again' => $spin_again]);
$conn->close();
?>