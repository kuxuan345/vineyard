<?php
require 'base_checkout.php';

// Check if product ID is passed
if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$product_id = intval($_GET['id']);
$conn = new mysqli("localhost", "root", "", "alcohol_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product details
$product_query = ('SELECT * FROM products 
                  WHERE id = ?');
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Fetch product prices for different years (without LIMIT)
$price_query = ('SELECT year, price 
                FROM prices 
                WHERE product_id = ? 
                ORDER BY year DESC');
$stmt = $conn->prepare($price_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$price_result = $stmt->get_result();


$price_options = [];
while ($row = $price_result->fetch_assoc()) {
    $price_options[] = $row;
}

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];


// Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'addCart') {
    $quantity = intval($_POST['quantity']);
    $product_name = $_POST['product_name'];
    $price_per_item = floatval($_POST['price']);
    $year = intval($_POST['year']); // Get the selected year
    $subtotal = $price_per_item * $quantity;

    // Begin transaction to add item to the cart
    $conn->begin_transaction();

    try {
        // Check if the item already exists in the cart
        $stmt = $conn->prepare('SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? AND year = ? AND price = ?');
        $stmt->bind_param("iiid", $userId, $product_id, $year, $price_per_item);
        $stmt->execute();
        $existing_item = $stmt->get_result()->fetch_assoc();

        if ($existing_item) {
            // If item exists, update the quantity and subtotal
            $new_quantity = $existing_item['quantity'] + $quantity;
            $new_subtotal = $price_per_item * $new_quantity;

            $stmt = $conn->prepare('UPDATE cart SET quantity = ?, subtotal = ? WHERE user_id = ? AND product_id = ? AND year = ? AND price = ?');
            $stmt->bind_param("diiidi", $new_quantity, $new_subtotal, $userId, $product_id, $year, $price_per_item);
        } else {
            // If item does not exist, insert a new row into the cart
            $stmt = $conn->prepare('INSERT INTO cart (user_id, product_id, product_name, year, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param("iisiddd", $userId, $product_id, $product_name, $year, $quantity, $price_per_item, $subtotal);
        }

        $stmt->execute();
        $conn->commit();

        // Redirect to avoid form resubmission
        header('Location: details.php?id=' . $product_id);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error adding to cart: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/detail.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Alcohol Details</title>
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<div class="product-details-container">

<a href="javascript:void(0);" class="back-button" title="Back" onclick="window.history.back();">
    <i class="fa fa-arrow-left"></i>
</a>
    <div class="product-image">
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <div class="product-info">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p style="font-size:18px">4.8 <span class="star">&#9733;</p>

        <p class="description"><?= htmlspecialchars($product['description']) ?></p>

        <div class="year-select-container">
                <label for="year-select">Price:</label>
                <select id="year-select" name="price">
                    <?php foreach ($price_options as $option) { ?>
                        <option value="<?= htmlspecialchars($option['price']) ?>" data-year="<?= htmlspecialchars($option['year']) ?>">
                            <?= htmlspecialchars($option['year']) . " - MYR " . number_format($option['price'], 2) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

        <div class="display-related-details">
            <p><span class="d1">Type</span> <span class="d2" style="margin-left: 131px;"><?= htmlspecialchars($product['type']) ?></span></p>
            <p><span class="d1">Cuvèe</span> <span class="d2" style="margin-left: 121px;"><?= htmlspecialchars($product['cuvee']) ?></span></p>
            <p><span class="d1">Alcohol</span> <span class="d2" style="margin-left: 109px;"><?= htmlspecialchars($product['alcohol_content']) ?></span></p>
            <p><span class="d1">Ingredient</span> <span class="d2" style="margin-left: 88px;"><?= htmlspecialchars($product['ingredient']) ?></span></p>
        </div>
        <hr width="100%" size="2" style="margin-bottom: 30px; margin-top: 20px;">

        <form method="post" action="">
            <input type="hidden" name="action" value="addCart">
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
            <input type="hidden" name="price" id="price-hidden" value="<?= isset($price_options[0]['price']) ? htmlspecialchars($price_options[0]['price']) : '0.00' ?>">
            <input type="hidden" name="year" id="year-hidden" value="<?= isset($price_options[0]['year']) ? htmlspecialchars($price_options[0]['year']) : '' ?>">

            <div class="quantity-container">
                <label for="quantity">Quantity:</label>
                <div class="qtycontrol">
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="15" />
        </form>
    </div>

    <div class="button-container">
        <button type="submit" class="add-to-cart">ADD TO CART</button>
        <form method="post" action="wishlist.php" class="wishlist">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
            <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
            <input type="hidden" name="year" id="wishlist-year" value="<?= isset($price_options[0]['year']) ? htmlspecialchars($price_options[0]['year']) : '' ?>">
            <input type="hidden" name="price" id="wishlist-price" value="<?= isset($price_options[0]['price']) ? htmlspecialchars($price_options[0]['price']) : '0.00' ?>">
            <input type="submit" name="add_to_wishlist" value="ADD TO WISHLIST">
        </form>
    </div>
    
</div>

</html>
<script>
    // Update the hidden price input based on the selected year
    document.getElementById('year-select').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var selectedPrice = selectedOption.value;
        var selectedYear = selectedOption.getAttribute('data-year');

        document.getElementById('price-hidden').value = selectedPrice;
        document.getElementById('year-hidden').value = selectedYear;
    });

</script>