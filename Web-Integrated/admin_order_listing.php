<?php require 'base_checkout.php';
// -------------------------------------------------------------------------------------------------------

$order_id = $_GET['order_id'] ?? null;

// Fetch Order Items
$stm = $_db->prepare('SELECT * 
                      FROM checkout c, order_items o
                      WHERE c.order_id = o.order_id');
$stm->execute();
$order = $stm->fetchAll(PDO::FETCH_OBJ);

include('admin_header.php');
// --------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDER LISTING</title>
    <link rel="stylesheet" href="css/admin_order_listing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/orderlisting.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/menu.css">
</head>

<body>
<div class="order-list">
    <h1>MEMBER'S ORDER LISTING</h1>

    <table class="listing">
        <tr>
            <th>Order ID</th>
            <th>DateTime</th>
            <th>User ID</th>
            <th>ItemCount</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price (RM)</th>
            <th>Subtotal (RM)</th>            
            <th>Total (RM)</th>
            <th>Payment Method</th>
        </tr>

        <?php foreach ($order as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order->order_id) ?></td>
                <td><?= htmlspecialchars($order->datetime) ?></td>
                <td><?= htmlspecialchars($order->user_id) ?></td>
                <td><?= htmlspecialchars($order->item_count) ?></td>
                <td><?= htmlspecialchars($order->product_name) ?></td>
                <td><?= htmlspecialchars($order->quantity) ?></td>
                <td><?= htmlspecialchars($order->price) ?></td>
                <td><?= htmlspecialchars($order->subtotal) ?></td>
                <td><?= htmlspecialchars($order->total) ?></td>
                <td><?= htmlspecialchars($order->method) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>

</html>