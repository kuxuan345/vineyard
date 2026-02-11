<?php
if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$product_id = intval($_GET['id']);
$conn = new mysqli("localhost", "root", "", "alcohol_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product details to show them
$product_query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Check if the form was submitted to add additional details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $cuvee = $_POST['cuvee'];
    $alcohol_content = $_POST['alcohol_content'];
    $ingredient = $_POST['ingredient'];
    $price = $_POST['price'];
    $year = $_POST['year'];

    // Insert the additional details into the products table
    $update_query = "UPDATE products SET type = ?, cuvee = ?, alcohol_content = ?, ingredient = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssi", $type, $cuvee, $alcohol_content, $ingredient, $product_id);

    if ($stmt->execute()) {
        // Insert price into prices table
        $price_query = "INSERT INTO prices (product_id, year, price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($price_query);
        $stmt->bind_param("isd", $product_id, $year, $price);

        if ($stmt->execute()) {
            echo "Product details saved successfully!";
        } else {
            echo "Error saving price details.";
        }
    } else {
        echo "Error updating product details.";
    }
}

include 'admin_header.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/add_product.css">
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <div class="container">
        <h1>Edit Product Details</h1>

        <form method="POST">
            <label for="type">Type:</label>
            <input type="text" name="type" required><br><br>

            <label for="cuvee">Cuvée:</label>
            <input type="text" name="cuvee" required><br><br>

            <label for="alcohol_content">Alcohol Content:</label>
            <input type="text" name="alcohol_content" required><br><br>

            <label for="ingredient">Ingredients:</label>
            <input type="text" name="ingredient" required><br><br>

            <label for="price">Price (MYR):</label>
            <input type="number" name="price" step="0.01" required><br><br>

            <label for="year">Year:</label>
            <input type="text" name="year" required><br><br>

            <button type="submit">Save Product Details</button>
        </form>
    </div>
</body>
</html>
