<?php include 'base_checkout.php';

// Get the order_id from the query string
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("Order ID is missing.");
}

// Fetch order details
$stm = $_db->prepare('SELECT * FROM checkout WHERE order_id = ?');
$stm->execute([$order_id]);
$order = $stm->fetch(PDO::FETCH_OBJ); // Fetch as an object

if (!$order) {
    die("Order not found.");
}

// Fetch order items
$stm = $_db->prepare('SELECT * FROM order_items WHERE order_id = ?');
$stm->execute([$order_id]);
$items = $stm->fetchAll(PDO::FETCH_OBJ); // Fetch as objects

// --------------------------------------------------------------------------------------------------------
include ('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/orderDetails.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/background.css">
</head>
<body>
    <div class="order-box">
    <a href="javascript:void(0);" class="back-btn" title="Back" onclick="window.history.back();">
        <i class="fa fa-arrow-left"></i>
    </a>
        <h1>Order Details</h1>
        <table class="history">
            <tr>
                <th>Order ID</th>
                <th>DateTime</th>
                <th>Total (RM)</th>
                <th>Payment Method</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($order->order_id) ?></td>
                <td><?= htmlspecialchars($order->datetime) ?></td>
                <td>RM <?= htmlspecialchars($order->total) ?></td>
                <td><?= htmlspecialchars($order->method) ?></td>
            </tr>
        </table>

        <hr>

        <h2>Items in Order</h2>
        <table class="history">
            <tr>
                <th>Product Name</th>
                <th>Year</th>
                <th>Price (RM)</th>
                <th>Quantity</th>
                <th>Subtotal (RM)</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->product_name) ?></td>
                    <td><?= htmlspecialchars($item->year) ?></td>
                    <td>RM <?= htmlspecialchars($item->price) ?></td>
                    <td><?= htmlspecialchars($item->quantity) ?></td>
                    <td>RM <?= htmlspecialchars($item->subtotal) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <form action="mem_order_history.php" method="get">
            <button type="submit" class="btn-back">DONE</button>
        </form>
    </div>
</body>
</html>