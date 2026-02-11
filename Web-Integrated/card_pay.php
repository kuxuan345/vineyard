<?php
require 'base_checkout.php';

// -------------------------------------------------------------------------------------------------------

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if (is_post() && isset($_POST['card'])) {
    if ($_POST['card'] == 'PAY') {
        $name_on_card = req('name_on_card');
        $card_number = req('card_number');
        $exp_month = strtoupper(req('exp_month')); // Convert to Uppercase
        $exp_year = req('exp_year');
        $cvv = req('cvv');
        global $month;

        // Validation for name
        if (!$name_on_card) {
            $_err['name_on_card'] = 'Name is required!';
        } else if (!preg_match('/^[a-zA-Z\s]{1,100}$/', $name_on_card)) {
            $_err['name_on_card'] = 'Name on card must consist of alphabets only and be up to 100 characters.';
        }

        // Validation for card number
        if (!$card_number) {
            $_err['card_number'] = 'Card number is required!';
        } else if (!preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $card_number)) {
            $_err['card_number'] = 'Please enter a valid card number (FORMAT: ####-####-####-####).';
        }

        // Validation for expire month
        if (!$exp_month) {
            $_err['exp_month'] = 'Expiration month is required!';
        } else if (!in_array($exp_month, $month)) {
            $_err['exp_month'] = 'Please enter a valid expiration month (e.g., DEC or DECEMBER).';
        } else if ($exp_year == 2024 && array_search($exp_month, $month) < array_search('DECEMBER', $month)) {
            $_err['exp_month'] = 'Expiration month must not be before December 2024.';
        }

        // Validation for expire year
        if (!$exp_year) {
            $_err['exp_year'] = 'Expiration year is required!';
        } else if (!preg_match('/^\d{4}$/', $exp_year)) {
            $_err['exp_year'] = 'Please enter a valid 4-digit expiration year.';
        } else if ($exp_year < 2024) {
            $_err['exp_year'] = 'Expiration year must not be before 2024.';
        }

        // Validation for CVV
        if (!$cvv) {
            $_err['cvv'] = 'CVV is required!';
        } else if (!preg_match('/^\d{3}$/', $cvv)) {
            $_err['cvv'] = 'Please enter a valid 3-digit CVV.';
        }

        if (empty($_err)) {

            try {
                $_db->beginTransaction();

                // Step 1: Fetch products and their quantities for the order
                $stmtOrderDetails = $_db->prepare('SELECT product_name, quantity FROM order_items WHERE order_id = ?');
                $stmtOrderDetails->execute([$orderId]);
                $orderDetails = $stmtOrderDetails->fetchAll(PDO::FETCH_ASSOC);

                if (!$orderDetails) {
                    throw new Exception('No products found for the order.');
                }

                // Step 2: Update product quantities
                foreach ($orderDetails as $item) {
                    $stmtUpdateProduct = $_db->prepare('UPDATE products SET quantity = quantity - ? WHERE name = ? AND quantity >= ?');
                    $stmtUpdateProduct->execute([$item['quantity'], $item['product_name'], $item['quantity']]);

                    if ($stmtUpdateProduct->rowCount() === 0) {
                        throw new Exception('Failed to update product stock. Insufficient stock or invalid product.');
                    }
                }

                $stmt = $_db->prepare('DELETE FROM cart WHERE user_id = ?');
                $stmt->execute([$userId]);

                // Commit the transaction
                $_db->commit();

                // Redirect to a confirmation page or reload the current page
                redirect('mem_order_history.php');
            } catch (Exception $e) {
                // Rollback the transaction in case of error
                $_db->rollBack();
                die('Transaction failed: ' . $e->getMessage());
            }
        }
    }

    // Check if the user is submitting a cancel request
    if ($_POST['card'] == 'CANCEL' && $orderId) {
        try {
            // Begin transaction
            $_db->beginTransaction();

            // Check if the order exists in the checkout table
            $stmtCheck = $_db->prepare('SELECT * FROM checkout WHERE user_id = ? AND order_id = ?');
            $stmtCheck->execute([$userId, $orderId]);
            $order = $stmtCheck->fetch();

            if (!$order) {
                echo 'Order not found for this user_id and order_id';
                exit();
            }

            // Proceed to delete the order
            $stmtDelete = $_db->prepare('DELETE FROM checkout WHERE user_id = ? AND order_id = ?');
            $stmtDelete->execute([$userId, $orderId]);

            if ($stmtDelete->rowCount() > 0) {
                // Commit the transaction
                $_db->commit();
                header('Location: checkout.php');
                exit();
            } else {
                // Rollback the transaction if no rows were affected
                $_db->rollBack();
                echo 'No rows affected. Order not deleted.';
            }
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $_db->rollBack();
            die('Error: ' . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/card_pay.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Card Payment</title>
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
<div class="container1">
    <form method="post" action="card_pay.php?order_id=<?= htmlspecialchars($orderId) ?>">
        <div class="row1">
            <div class="col1">

                <h2 class="title1">Card Details</h2>

                <div class="inputBox1">
                    <span>Cards accepted :</span>
                    <img src="images/card_img.png" alt="card example images">
                </div>

                <div class="inputBox1">
                    <span>Name on card :</span>
                    <input type="text" name="name_on_card" value="<?= htmlspecialchars($name_on_card ?? '') ?>">
                    <?php if (isset($_err['name_on_card'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['name_on_card']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="inputBox1">
                    <span>Credit/Debit card number :</span>
                    <input type="text" name="card_number" placeholder="****-****-****-****" value="<?= htmlspecialchars($card_number ?? '') ?>">
                    <?php if (isset($_err['card_number'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['card_number']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="inputBox1">
                    <span>Exp month :</span>
                    <input type="text" name="exp_month" placeholder="January" value="<?= htmlspecialchars($exp_month ?? '') ?>">
                    <?php if (isset($_err['exp_month'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['exp_month']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="inputBox1">
                    <span>Exp year :</span>
                    <input type="text" name="exp_year" placeholder="****" value="<?= htmlspecialchars($exp_year ?? '') ?>">
                    <?php if (isset($_err['exp_year'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['exp_year']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="inputBox1">
                    <span>CVV (number behind your card) :</span>
                    <input type="text" name="cvv" placeholder="***" value="<?= htmlspecialchars($cvv ?? '') ?>">
                    <?php if (isset($_err['cvv'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['cvv']) ?></div>
                    <?php endif; ?>
                </div>

                <button class="card_pay" type="submit" name="card" value="PAY">PAY</button>
                <button class="card_pay" type="submit" name="card" value="CANCEL">CANCEL</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>