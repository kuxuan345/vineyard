<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "alcohol_store");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Fetch categories for the top menu
$categories = $conn->query("SELECT DISTINCT type FROM categories");


// Fetch products based on the selected category and subcategory
$products = null;
if (isset($_GET['category']) && $_GET['category'] != "all") {
    $category = $_GET['category'];

    if (isset($_GET['subcategory'])) {
        $subcategory = $_GET['subcategory'];

        // Fetch products for a specific category and subcategory
        $query = "SELECT products.id, products.name, products.image
                  FROM products
                  JOIN categories ON products.category_id = categories.id
                  WHERE categories.type = ? AND categories.name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $category, $subcategory);
    } else {
        // Fetch products for a specific category only
        $query = "SELECT products.id, products.name, products.image
                  FROM products
                  JOIN categories ON products.category_id = categories.id
                  WHERE categories.type = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
    }

    $stmt->execute();
    $products = $stmt->get_result();
} else {
    // Fetch all products when "ALL CATEGORY" is selected
    $query = "SELECT id, name, image FROM products";
    $products = $conn->query($query);
}
include('header.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cart.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Our Vintage</title>
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <!-- Top Menu -->
    <div class="menu">
        <nav>
            <a href="?category=all">ALL CATEGORY</a>
            <?php while ($row = $categories->fetch_assoc()): ?>
                <a href="?category=<?= $row['type'] ?>"><?= strtoupper($row['type']) ?></a>
            <?php endwhile; ?>
        </nav>
    </div>
    <!-- Subcategory Menu -->
    <?php if (isset($_GET['category']) && $_GET['category'] != "all"): ?>
        <div class="subcategory-menu">
            <?php
            $category = $_GET['category'];
            $subcategories = $conn->query("SELECT name FROM categories WHERE type = '$category'");
            while ($row = $subcategories->fetch_assoc()):
            ?>
                <a href="?category=<?= $category ?>&subcategory=<?= $row['name'] ?>"><?= $row['name'] ?></a>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>


    <!-- Product Display -->
    <div class="cart-container">
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="cart-item">
                    <a href="details.php?id=<?= $product['id'] ?>">
                        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" style="padding-top: 68px;">
                    </a>
                    <div class="cart-item-name">
                        <a href="details.php?id=<?= $product['id'] ?>"><?= $product['name'] ?></a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; font-size: 18px; color: #666;">No products available for this selection.</p>
        <?php endif; ?>
    </div>
</body>

</html>