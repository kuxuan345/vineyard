<?php require 'base_checkout.php';

// -------------------------------------------------------------------------------------------------------

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];

// Retrieve the order ID from POST or GET
$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : (isset($_GET['order_id']) ? (int)$_GET['order_id'] : null);

// Redirect if order ID is missing
if (!$orderId) {
    die('Order ID is missing.');
}

// Reset bank selection on page refresh
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($_SESSION['select_bank']);
}

// Initialize variables
$s = temp('data');
if (!$s) {
    temp('info', 'Please select a bank');
}

if (is_post() && isset($_POST['bank'])) {
    // Handle form submission
    $select_bank = req('bank_method');
    $username = req('username');
    $password = req('password');

    if ($_POST['bank'] === 'CONFIRM') {
        // Validate bank selection
        if (!$select_bank || !array_key_exists($select_bank, $_bankings)) {
            $_err['bank_method'] = 'Please select a bank.';
        } else {
            $_SESSION['select_bank'] = $select_bank;
        }
    }

    if ($_POST['bank'] === 'PAY') {
        // Validate username
        if (!$username) {
            $_err['username'] = 'Username is required.';
        } elseif (!preg_match('/^(?=.*[a-zA-Z])(?=.*[\d])(?=.*[^\w\s]).{8,15}$/', $username)) {
            $_err['username'] = 'Username must be 8-15 characters, include alphabets, numbers, and special symbols.';
        }

        // Validate password
        if (!$password) {
            $_err['password'] = 'Password is required.';
        } elseif (!preg_match('/^(?=.*[a-zA-Z])(?=.*[\d])(?=.*[^\w\s]).{10,}$/', $password)) {
            $_err['password'] = 'Password must be at least 10 characters, include alphabets, numbers, and special symbols.';
        }

        // Process payment if no errors
        if (empty($_err)) {
            try {
                // Fetch products and their quantities for the order
                $stmtOrderDetails = $_db->prepare('SELECT product_name, quantity FROM order_items WHERE order_id = ?');
                $stmtOrderDetails->execute([$orderId]);
                $orderDetails = $stmtOrderDetails->fetchAll(PDO::FETCH_ASSOC);

                if (!$orderDetails) {
                    throw new Exception('No products found for the order.');
                }

                $_db->beginTransaction();

                // Update product quantities
                foreach ($orderDetails as $item) {
                    $stmtUpdateProduct = $_db->prepare(
                        'UPDATE products SET quantity = quantity - ? WHERE name = ? AND quantity >= ?'
                    );
                    $stmtUpdateProduct->execute([$item['quantity'], $item['product_name'], $item['quantity']]);

                    if ($stmtUpdateProduct->rowCount() === 0) {
                        throw new Exception('Failed to update product stock. Insufficient stock or invalid product.');
                    }
                }

                // Clear user's cart
                $stmt = $_db->prepare('DELETE FROM cart WHERE user_id = ?');
                $stmt->execute([$userId]);

                // Commit the transaction
                $_db->commit();

                // Redirect to order history
                redirect('mem_order_history.php');
            } catch (Exception $e) {
                // Rollback the transaction in case of error
                $_db->rollBack();
                die('Transaction failed: ' . $e->getMessage());
            }
        }
    }

    if ($_POST['bank'] === 'CANCEL') {
        try {
            $_db->beginTransaction();

            // Check if the order exists in the checkout table
            $stmtCheck = $_db->prepare('SELECT * FROM checkout WHERE user_id = ? AND order_id = ?');
            $stmtCheck->execute([$userId, $orderId]);
            $order = $stmtCheck->fetch();

            if (!$order) {
                throw new Exception('Order not found for this user_id and order_id.');
            }

            // Delete the order
            $stmtDelete = $_db->prepare('DELETE FROM checkout WHERE user_id = ? AND order_id = ?');
            $stmtDelete->execute([$userId, $orderId]);

            if ($stmtDelete->rowCount() > 0) {
                $_db->commit();
                header('Location: checkout.php');
                exit();
            } else {
                $_db->rollBack();
                throw new Exception('No rows affected. Order not deleted.');
            }
        } catch (Exception $e) {
            $_db->rollBack();
            die('Error: ' . $e->getMessage());
        }
    }
}

// Retrieve selected bank from session
$select_bank = $_SESSION['select_bank'] ?? req('bank_method');

// -------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/online_payment.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Online Payment</title>
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="ob_container">
        <form method="post" action="online_payment.php">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderId); ?>">
            <div class="ob_row">
                <div class="ob_col">
                    <h2 class="ob_title">Online Banking</h2>
                    <p>Please select your preferred bank: </p>
                    <div class="option">
                        <select name="bank_method">
                            <option value="">Select a bank</option>
                            <?php foreach ($_bankings as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($select_bank == $key) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($_err['bank_method'])): ?>
                            <div class="error"><?= htmlspecialchars($_err['bank_method']) ?></div>
                        <?php endif; ?>
                    </div>
                    <button class="ob_pay1" type="submit" name="bank" value="CONFIRM">CONFIRM</button>

                    <?php if ($select_bank && isset($bank_img[$select_bank])): ?>
                        <div class="bank_img">
                            <img src="<?= $bank_img[$select_bank]; ?>" alt="<?= $_bankings[$select_bank]; ?>" style="max-width: 200px;">
                            <h3><?= $_bankings[$select_bank]; ?></h3>
                        </div>
                    <?php endif; ?>

                    <?php if ($select_bank): ?>
                        <div class="bank_pay">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" maxlength="30">
                            <?php if (!empty($_err['username'])): ?>
                                <div class="error"><?= htmlspecialchars($_err['username']) ?></div>
                            <?php endif; ?>

                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" maxlength="30">
                            <?php if (!empty($_err['password'])): ?>
                                <div class="error"><?= htmlspecialchars($_err['password']) ?></div>
                            <?php endif; ?>

                            <button class="ob_pay2" type="submit" name="bank" value="PAY">PAY</button>
                            <button class="ob_pay2" type="submit" name="bank" value="CANCEL">CANCEL</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
