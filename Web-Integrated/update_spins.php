<?php
// update_spins.php
session_start();
if (!isset($_SESSION['user']['id']) || !isset($_POST['action']) || $_POST['action'] !== 'use_spin') {
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

// Begin transaction
$conn->begin_transaction();

try {
    // Check available spins
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

    if ($totalTransactions - $spinsUsed <= 0) {
        throw new Exception('No spins available');
    }

    // Update or insert spin usage
    $updateQuery = "INSERT INTO user_spins (user_id, spins_used) 
                   VALUES (?, 1) 
                   ON DUPLICATE KEY UPDATE spins_used = spins_used + 1";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'remaining_spins' => $totalTransactions - ($spinsUsed + 1)]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>