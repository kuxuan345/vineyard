<?php include 'base_checkout.php';

// -------------------------------------------------------------------------------------------------------

// Check if user is logged in
$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
    die("User is not logged in.");
}

// Fetch orders belonging to the user in descending order
$stm = $_db->prepare('SELECT * FROM checkout WHERE user_id = ? ORDER BY order_id DESC');
$stm->execute([$user_id]);
$orders = $stm->fetchAll(PDO::FETCH_OBJ); // Use PDO::FETCH_OBJ to get objects

include('header.php');
// --------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/orderHis.css">

</head>

<body>
<div class="order-box">
<a href="menu.php" class="back-btn" title="Back">
    <i class="fa fa-arrow-left"></i>
</a>
    <h1>Order History</h1>
    <hr></hr>
    <table class="order-history">
        <tr>
            <th>Order ID</th>
            <th>DateTime</th>
            <th>ItemCount</th>
            <th>Total (RM)</th>
            <th>Payment Method</th>
            <th>Action</th>
        </tr>

        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order->order_id) ?></td>
                <td><?= htmlspecialchars($order->datetime) ?></td>
                <td><?= htmlspecialchars($order->item_count) ?></td>
                <td>RM <?= number_format($order->total, 2) ?></td>
                <td><?= htmlspecialchars($order->method) ?></td>
                <td>
                    <form action="mem_order_details.php" method="get">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                        <button type="submit" class="details-btn">Details</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>

</html>