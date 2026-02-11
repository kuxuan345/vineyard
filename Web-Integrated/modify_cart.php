<?php
require 'base_checkout.php';

$user_id = $_SESSION['user']['id'] ?? null;

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}


// Get the cart_id from the request (e.g., passed via GET)
$cart_id = $_GET['cart_id'] ?? null;



// Fetch cart items, grouped by name and price
$stm = $_db->prepare('
    SELECT product_name,year, price, SUM(quantity) AS total_quantity, SUM(subtotal) AS total_subtotal
    FROM cart
    WHERE user_id = ?
    GROUP BY product_name, price
');
$stm->execute([$user_id]);
$items = $stm->fetchAll(PDO::FETCH_ASSOC); // Fetch all grouped records


if (!$items) {
    include('header.php');

    echo '
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/myCart.css">

    <div class="cart-container">
        <a href="cart.php" class="back-btn">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h2 class="cart-title">MY CART</h2>
        <p class="empty-message">Your cart is currently empty. Browse our menu to add items.</p>
    </div>
    ';
    exit();
}


$total = array_sum(array_column($items, 'total_subtotal')); // Calculate total


if (isset($_POST['modify'])) {
    if ($_POST['modify'] == 'CHECKOUT') {
        try {
            // Redirect to the checkout page with the current cart ID
            header('Location: checkout.php?cart_id=' . urlencode($cart_id));
            exit();
        } catch (Exception $e) {
            error_log("Error during checkout redirect: " . $e->getMessage());
            $_err['general'] = 'An error occurred while proceeding to checkout. Please try again.';
        }
    }
}


if (isset($_POST['update_quantity'])) {
    try {
        // Get the product name, price, and new quantity from the form
        $product_name = $_POST['product_name'];
        $price = $_POST['price'];
        $new_quantity = intval($_POST['quantity']);


        if ($new_quantity < 0) {
            throw new Exception("Invalid quantity.");
        }


        if ($new_quantity == 0) {
            // If quantity is 0, delete the item from the cart
            $stmDelete = $_db->prepare('
                DELETE FROM cart
                WHERE user_id = ? AND product_name = ? AND price = ?
            ');
            $stmDelete->execute([$user_id, $product_name, $price]);


            // Redirect back to the cart page after deletion
            header('Location: modify_cart.php?cart_id=' . urlencode($cart_id));
            exit();
        } else {
            // Fetch the price of the item
            $stmPrice = $_db->prepare('
                SELECT price
                FROM cart
                WHERE user_id = ? AND product_name = ? AND price = ?
            ');
            $stmPrice->execute([$user_id, $product_name, $price]);
            $item = $stmPrice->fetch(PDO::FETCH_ASSOC);


            if (!$item) {
                throw new Exception("Item not found.");
            }


            $price_per_item = $item['price'];
            $new_subtotal = $price_per_item * $new_quantity;


            // Update the cart table with the new quantity and subtotal
            $stmUpdate = $_db->prepare('
                UPDATE cart
                SET quantity = ?, subtotal = ?
                WHERE user_id = ? AND product_name = ? AND price = ?
            ');
            $stmUpdate->execute([$new_quantity, $new_subtotal, $user_id, $product_name, $price]);


            // Redirect back to the cart page to reflect changes
            header('Location: modify_cart.php?cart_id=' . urlencode($cart_id));
            exit();
        }
    } catch (Exception $e) {
        error_log("Error updating quantity: " . $e->getMessage());
        $_err['general'] = 'An error occurred while updating the quantity. Please try again.';
    }

    
}
if (isset($_POST['clear_cart'])) {
    try {
        $stmClear = $_db->prepare('DELETE FROM cart WHERE user_id = ?');
        $stmClear->execute([$user_id]);

        if ($stmClear->rowCount() == 0) {
            error_log("Clear cart failed: No rows affected for user ID $user_id");
        } else {
            error_log("Cart cleared successfully for user ID $user_id");
        }

        header('Location: modify_cart.php?cart_id=' . urlencode($cart_id));
        exit();
    } catch (PDOException $e) {
        error_log("Error clearing cart: " . $e->getMessage());
        $_err['general'] = 'An error occurred while clearing the cart. Please try again.';
    }
}

include('header.php');
// ----------------------------------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/myCart.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/background.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Our Vintage</title>
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<div class="cart-container">
    <a href="cart.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back
    </a>
    <h2 class="cart-title">MY CART</h2>
    <hr>
    </hr>
    <table class="cart-detail">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Year</th>
                <th>Price (RM)</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td class="item-name"><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= htmlspecialchars($item['year'] ?? 'N/A') ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td>
                        <!-- Form to update quantity -->
                        <form method="post" action="modify_cart.php?cart_id=<?= urlencode($cart_id) ?>" class="quantity-form">
                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($item['product_name']) ?>">
                            <input type="hidden" name="year" value="<?= htmlspecialchars($item['year']) ?>">
                            <input type="hidden" name="price" value="<?= htmlspecialchars($item['price']) ?>">
                            <input type="number" name="quantity" value="<?= $item['total_quantity'] ?>" min="0" max="15" class="quantity-input">
                            <button type="submit" name="update_quantity" value="1" class="update-btn">UPDATE</button>
                        </form>
                    </td>
                    <td><?= number_format($item['total_subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="total">Total</th>
                <th><?= number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="button-row">
        <div class="clear-cart">
            <form method="post" class="clear-cart-form">
                <button class="clear-cart-btn" type="submit" name="clear_cart" >CLEAR CART</button>
            </form>
        </div>

        <div class="checkout-section">
            <?php if ($_SESSION['user']['role'] == 'Member'): ?>
                <form method="post" class="checkout-form">
                    <button class="checkout-btn" type="submit" name="modify" value="CHECKOUT">CHECK OUT</button>
                </form>
            <?php else: ?>
                <p class="login-prompt">Please <a href="/login.php" class="login-link">login</a> as a member to checkout.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.querySelector('.clear-cart-btn').addEventListener('click', function (e) {
        if (!confirm('Are you sure you want to clear your cart?')) {
            e.preventDefault(); // Stop the form submission
        }
    });
</script>

</html>
