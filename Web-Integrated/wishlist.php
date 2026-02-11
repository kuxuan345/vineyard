<?php
require 'base_checkout.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// Handle Remove All
if (isset($_POST['remove'])) {
    try {
        $stm = $_db->prepare('DELETE FROM wishlist WHERE user_id = ?');
        $stm->execute([$user_id]);
        header('Location: wishlist.php');
        exit();
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
}

// Handle Remove Single Item
if (isset($_POST['remove1'])) {
    $item_id = $_POST['item_id'];
    try {
        $stm = $_db->prepare('DELETE FROM wishlist WHERE id = ? AND user_id = ?');
        $stm->execute([$item_id, $user_id]);

        header('Location: wishlist.php');
        exit();
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    try {
        // Get wishlist item details
        $stm = $_db->prepare('SELECT * FROM wishlist WHERE id = ? AND user_id = ?');
        $stm->execute([$item_id, $user_id]);
        $wishlist_item = $stm->fetch(PDO::FETCH_ASSOC);

        if ($wishlist_item) {
            // Check if item exists in cart
            $stm = $_db->prepare('SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND year = ? AND price = ?');
            $stm->execute([$user_id, $wishlist_item['product_id'], $wishlist_item['year'], $wishlist_item['price']]);
            $cart_item = $stm->fetch(PDO::FETCH_ASSOC);

            if ($cart_item) {
                // Update quantity if exists
                $new_quantity = $cart_item['quantity'] + 1;
                $new_subtotal = $cart_item['price'] * $new_quantity;
                $stm = $_db->prepare('UPDATE cart SET quantity = ?, subtotal = ? WHERE cart_id = ?');
                $stm->execute([$new_quantity, $new_subtotal, $cart_item['cart_id']]);
            } else {
                // Insert new cart item
                $stm = $_db->prepare('INSERT INTO cart (user_id, product_id, product_name, year, quantity, price, subtotal) VALUES (?, ?, ?, ?, 1, ?, ?)');
                $stm->execute([$user_id, $wishlist_item['product_id'], $wishlist_item['product_name'], $wishlist_item['year'], $wishlist_item['price'], $wishlist_item['price']]);
            }
        }
        header('Location: wishlist.php');
        exit();
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
}

// Add to wishlist (your existing code)
if (isset($_POST['add_to_wishlist'])) {
    $product_id = $_POST['product_id'] ?? null;
    $product_name = $_POST['product_name'] ?? null;
    $image = $_POST['image'] ?? null;
    $year = $_POST['year'] ?? null;
    $price = $_POST['price'] ?? null;

    if ($product_id && $product_name && $image && $year && $price) {
        try {
            $stm = $_db->prepare('SELECT id FROM wishlist WHERE product_id = ? AND user_id = ?');
            $stm->execute([$product_id, $user_id]);
            $existing_item = $stm->fetch(PDO::FETCH_ASSOC);

            if (!$existing_item) {
                $stm = $_db->prepare('INSERT INTO wishlist (product_id, product_name, image, year, price, user_id) VALUES (?, ?, ?, ?, ?, ?)');
                $stm->execute([$product_id, $product_name, $image, $year, $price, $user_id]);
            }
            header('Location: wishlist.php');
            exit();
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}

// Fetch wishlist items
try {
    $stm = $_db->prepare('SELECT * FROM wishlist WHERE user_id = ?');
    $stm->execute([$user_id]);
    $items = $stm->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}

include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WISHLIST</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/wishlist.css">
    <link rel="stylesheet" href="css/background.css">
    <link rel="stylesheet" href="css/menu.css">
</head>

<body>
    <div class="box">
    <a href="cart.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back
    </a>
        <h2>Wishlist</h2>
    <hr></hr>
        <table class="wishlist">
            <thead>
                <tr>
                    <th></th>
                    <th>Product Name</th>
                    <th>Year</th>
                    <th>Price</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>"></td>
                        <td class="item-name"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['year'] ?? 'N/A') ?></td>
                        <td><?= number_format($item['price'], 2) ?></td>
                        <td>
                            <form method="post" action="wishlist.php" class="remove1">
                                <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                <button type="submit" name="remove1" value="1" class="remove1">REMOVE</button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="wishlist.php">
                                <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">ADD</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="6" class="remove">
                        <form method="post" action="wishlist.php">
                            <input class="remove" type="submit" name="remove" value="REMOVE ALL">
                        </form>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>