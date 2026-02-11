<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "alcohol_store");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Basic product data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];  // Category selected by admin

    // Image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $validExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($imageExt), $validExts)) {
            die("Invalid image type.");
        }
        $newImageName = uniqid('product_', true) . '.' . $imageExt;
        $uploadPath = 'img/' . $newImageName;
        if (move_uploaded_file($imageTmpName, $uploadPath)) {
            // Insert into products table with selected category
            $query = "INSERT INTO products (name, description, image, category_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssi", $name, $description, $uploadPath, $category);
            if ($stmt->execute()) {
                $product_id = $stmt->insert_id;
                // Redirect to add details page with the product ID
                header("Location: add_details.php?id=$product_id");
                exit;
            } else {
                echo "Error adding product.";
            }
        } else {
            die("Error uploading image.");
        }
    }
}

// Fetch categories from the database
$categoriesQuery = "SELECT id, name, type FROM categories";
$categoriesResult = $conn->query($categoriesQuery);


include 'admin_header.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/add_product.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/background.css">
</head>
<body>
<div class="container">
    <h1>Add Product</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="name" required><br><br>
        
        <label>Description:</label>
        <textarea name="description" required></textarea><br><br>
        
        <label>Category:</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <?php while ($row = $categoriesResult->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['name'] . " - " . $row['type'] ?></option>
            <?php endwhile; ?>
        </select><br><br>
        
        <label>Upload Image:</label>
        <input type="file" name="image" accept="image/*" required><br><br>
        
        <button type="submit">Add Product</button>
    </form>
</div>
</body>
</html>
