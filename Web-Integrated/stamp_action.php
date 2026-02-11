<?php
session_start();
require '../payment/base_checkout.php'; // Adjust path as needed

if (!isset($_SESSION['user']['id'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

$userId = $_SESSION['user']['id'];

// Check if stamps are available
$stmtCheck = $_db->prepare('SELECT stamps_available FROM user_stamps WHERE user_id = ?');
$stmtCheck->execute([$userId]);
$stamps = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$stamps || $stamps['stamps_available'] <= 0) {
    die(json_encode(['success' => false, 'message' => 'No stamps available']));
}

try {
    $_db->beginTransaction();
    
    // Record stamp usage
    $stmtUseStamp = $_db->prepare('
        INSERT INTO stamp_transactions (user_id, stamps_changed, transaction_type) 
        VALUES (?, -1, "used")
    ');
    $stmtUseStamp->execute([$userId]);
    
    // Update available stamps
    $stmtUpdateStamps = $_db->prepare('
        UPDATE user_stamps 
        SET stamps_available = stamps_available - 1 
        WHERE user_id = ?
    ');
    $stmtUpdateStamps->execute([$userId]);
    
    $_db->commit();
    
    echo json_encode(['success' => true, 'message' => 'Stamp added successfully']);
} catch (Exception $e) {
    $_db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error processing stamp']);
}