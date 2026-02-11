<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "alcohol_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Delete related rows in child table
    $sql_delete_prices = "DELETE FROM prices WHERE product_id = $delete_id";
    if ($conn->query($sql_delete_prices) === TRUE) {
        // Now delete the product
        $sql_delete_product = "DELETE FROM products WHERE id = $delete_id";
        if ($conn->query($sql_delete_product) === TRUE) {
            echo "<script>alert('Product and related data deleted successfully'); window.location.href='product_listing.php';</script>";
        } else {
            echo "<script>alert('Error deleting product: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error deleting related data: " . $conn->error . "');</script>";
    }
}

// Handle search action
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM products WHERE name LIKE '%$search_query%'";
} else {
    $sql = "SELECT * FROM products";
}
$result = $conn->query($sql);
include('admin_header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/productlist.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/background.css">
    <title>Product Listing</title>
</head>
<body>
    <div class="container">
        <h1>Product Listing<a href="add_product.php" class="add-btn" title="Add a new product" style= "font-size: 16px; float: right; ">Add Product</a></h1>
        

        <div class="search-bar">
            <form method="GET" action="product_listing.php">
                <input type="text" name="search" placeholder="Search by wine name" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Cuvee</th>
                    <th>Alcohol Content</th>
                    <th>Ingredient</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                            <td><?php echo htmlspecialchars($row['cuvee']); ?></td>
                            <td><?php echo htmlspecialchars($row['alcohol_content']); ?></td>
                            <td><?php echo htmlspecialchars($row['ingredient']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td>
                                <a href="product_listing.php?delete_id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>