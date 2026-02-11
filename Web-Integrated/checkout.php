<?php require 'base_checkout.php';
// -------------------------------------------------------------------------------------------------------

if (isset($_POST['reset'])) {
    header('Location: edit_payment.php');
    exit();
}

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];

if (isset($userId)) {
    // Fetch edited info from the 'editinfo' table
    $stmtEditInfo = $_db->prepare('SELECT * FROM editinfo WHERE user_id = ? ORDER BY update_at DESC LIMIT 1');
    $stmtEditInfo->execute([$userId]);
    $editInfo = $stmtEditInfo->fetch(PDO::FETCH_ASSOC); // Fetch data from editinfo table

    // If no edited info is found, fetch the profile data
    if (!$editInfo) {
        // Fetch profile data from the 'profile' table
        $stmt = $_db->prepare('SELECT * FROM profile WHERE userId = ?');
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fallback to profile data if no editinfo found
        $name = $profile['name'];
        $contact_number = $profile['contact_number'];
        $address = $profile['address'];
        $state = $profile['state'];
        $city = $profile['city'];
        $postal_code = $profile['postal_code'];
    } else {
        // Use edited information from 'editinfo'
        $name = $editInfo['name'];
        $contact_number = $editInfo['contact_number'];
        $address = $editInfo['address'];
        $state = $editInfo['state'];
        $city = $editInfo['city'];
        $postal_code = $editInfo['postal_code'];
    }
}

// Fetch cart items for the given user_id
$stm = $_db->prepare('SELECT product_name, year, price, SUM(quantity) AS total_quantity, SUM(subtotal) AS total_subtotal
                     FROM cart
                     WHERE user_id = ?
                     GROUP BY product_name, price
                    ');
$stm->execute([$userId]);
$items = $stm->fetchAll(PDO::FETCH_ASSOC); // Fetch all matching records
$total_quantity = array_sum(array_column($items, 'total_quantity')); // Obtain the total quantity of all items

if (!$items) {
    die("No items found in the cart.");
}

// Calculate total and other amounts
$subtotal = array_sum(array_column($items, 'total_subtotal')); // Calculate the subtotal using the correct field
$tax = $subtotal * 0.15; // Assuming 15% tax
$shipping_fee = 25.00; // Flat shipping fee
$net_total = $subtotal + $tax + $shipping_fee; // Calculate the net total

if (is_post()) {
    $orderId = null; // Initialize orderId variable
    
    // Select Payment Method
    $method = req('method');

    // Payment Method Selection Validation
    if ($method == '') {
        $_err['method'] = 'Required';
    } else if (!array_key_exists($method, $_methods)) {
        $_err['method'] = 'Invalid Selection';
    }

    // Handle the "Pay Now" button click
    if (isset($_POST['pay']) && $_POST['pay'] == 'redirect') {
        $method = req('method');

        if (empty($method) || !array_key_exists($method, $_methods)) {
            $_err['method'] = 'Required';
        } elseif (!empty($items)) {
            if($method == "C"){
                $pay_method = "CARD PAYMENT";
            }else{
                $pay_method = "ONLINE BANKING";
            }
            
            try {
                $_db->beginTransaction();

                // Insert into checkout
                $stmtOrder = $_db->prepare(
                    'INSERT INTO checkout (user_id, item_count, total, method) 
                     VALUES (?, ?, ?, ?)'
                );

                if (!$stmtOrder->execute([$userId, $total_quantity, $net_total, $pay_method])) {
                    $_err['general'] = 'Error occurred while inserting order.';
                    throw new Exception("Order insert failed");
                }

                $orderId = $_db->lastInsertId();

                // Insert into order_items
                $stmtOrderItem = $_db->prepare(
                    'INSERT INTO order_items (order_id, product_name, year, quantity, price, subtotal) 
                     VALUES (?, ?, ?, ?, ?, ?)'
                );

                foreach ($items as $item) {
                    $year = (int) $item['year']; // Casting year to integer
                    if (!$stmtOrderItem->execute([
                        $orderId,
                        $item['product_name'],
                        $year,
                        $item['total_quantity'],
                        $item['price'],
                        $item['total_subtotal']
                    ])) {
                        error_log('Order Item Insert Error: ' . implode(", ", $stmtOrderItem->errorInfo()));
                        $_err['general'] = 'Error occurred while inserting order items.';
                        throw new Exception("Order item insert failed");
                    }
                }

                // Process stamps only if order was successful
                if ($orderId) {
                    // Insert stamps earned from this purchase
                    $stmtStamps = $_db->prepare('
                        INSERT INTO stamp_transactions (user_id, stamps_changed, transaction_type, order_id) 
                        VALUES (?, ?, "earned", ?)
                    ');
                    $stmtStamps->execute([$userId, $total_quantity, $orderId]);

                    // Update or insert into user_stamps
                    $stmtUpdateStamps = $_db->prepare('
                        INSERT INTO user_stamps (user_id, total_stamps, stamps_available) 
                        VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE 
                        total_stamps = total_stamps + VALUES(total_stamps),
                        stamps_available = stamps_available + VALUES(stamps_available)
                    ');
                    $stmtUpdateStamps->execute([$userId, $total_quantity, $total_quantity]);

                    // Set session variable for stamps earned
                    $_SESSION['stamps_earned'] = $total_quantity;
                }

                $_db->commit();

                // Redirect based on payment method
                switch ($method) {
                    case 'C':
                        header('Location: card_pay.php?order_id=' . $orderId);
                        exit();
                    case 'B':
                        header('Location: online_payment.php?order_id=' . $orderId);
                        exit();
                }
            } catch (Exception $e) {
                $_db->rollBack();
                error_log("Order processing error: " . $e->getMessage());
                $_err['general'] = 'An error occurred while processing your order.';
            }
        } else {
            $_err['general'] = 'No items found in the cart.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
  <link rel="stylesheet" href="css/checkout.css">
  <title>Checkout</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<div class="container">
    <a href="menu.php" class="home-btn">
        <i class="fa fa-home"></i>
    </a>
        <h2 class="title">Billing Info</h2>  
            <!--------------------------------- Billing Info --------------------------------->

            <form method="post" action="checkout.php">
                <input class="edit" type="submit" name="reset" value="EDIT INFO">
            </form>

            <div class="getInfo">
                <label>Name : </label>
                <span><?= htmlspecialchars($name ?? 'N/A') ?></span>
            </div>
            <div class="getInfo">
                <label>Phone Number : </label>
                <span><?= htmlspecialchars($contact_number ?? 'N/A') ?></span>
            </div>
            <div class="getInfo">
                <label>Address : </label>
                <span><?= htmlspecialchars($address ?? 'N/A') ?></span>
            </div>
            <div class="getInfo">
                <label>State : </label>
                <span><?= htmlspecialchars($state ?? 'N/A') ?></span>
            </div>
            <div class="getInfo">
                <label>City : </label>
                <span><?= htmlspecialchars($city ?? 'N/A') ?></span>
            </div>
            <div class="getInfo">
                <label>Postal Code : </label>
                <span><?= htmlspecialchars($postal_code ?? 'N/A') ?></span>
            </div>

            <hr>
            </hr>

            <!--------------------------------- Order Details --------------------------------->
            <h2 class="title">Order Details</h2>

            <table class="orderTable">
                <tr>
                    <th>ITEMS</th>
                    <th>YEAR</th>
                    <th>PRICE PER ITEM (RM)</th>
                    <th>QUANTITY</th>
                    <th>PRICE (RM)</th>
                </tr>

                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="itemName"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['year']) ?></td>
                        <td><?= number_format($item['price'], 2) ?></td>
                        <td><?= htmlspecialchars($item['total_quantity']) ?></td>
                        <td><?= number_format($item['total_subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <table class="displaySum">
                <tr>
                    <th>SubTotal: </th>
                    <td>RM <?= number_format($subtotal, 2) ?></td>
                </tr>
                <tr>
                    <th>Tax (15%): </th>
                    <td>RM <?= number_format($tax, 2) ?></td>
                </tr>
                <tr>
                    <th>Shipping Fee: </th>
                    <td>RM <?= number_format($shipping_fee, 2) ?></td>
                </tr>
                <tr>
                    <th>NetTotal: </th>
                    <td>RM <?= number_format($net_total, 2) ?></td>
                </tr>
            </table>

            <hr>
            </hr>

            <!--------------------------------- Select Payment --------------------------------->
            <h2 class="title">Payment</h2>

            <p>Please select your preferred payment method</p>

            <form method="post" class="form">
                <div class="selection">
                    <?= html_radios('method', $_methods) ?>
                    <?= err('method') ?><br>
                </div>

                <section>
                    <button class="pay" type="submit" name="pay" value="redirect">PAY NOW</button>
                </section>
            </form>
</div>
</html>
