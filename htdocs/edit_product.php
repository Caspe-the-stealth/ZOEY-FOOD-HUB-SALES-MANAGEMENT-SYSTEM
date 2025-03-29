<?php
include 'db_connection.php'; // Database connection

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Product ID");
}

$product_id = $_GET['id'];

// Fetch product details
$query = "SELECT * FROM products WHERE Product_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found");
}

$product = $result->fetch_assoc();

// Fetch categories for dropdown
$categoryResult = $conn->query("SELECT DISTINCT Category FROM products");
$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['Category'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category = $_POST['category'];

    $updateQuery = "UPDATE products SET Name = ?, Description = ?, Price = ?, Stock_Quantity = ?, Category = ? WHERE Product_ID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssdisi", $name, $description, $price, $stock_quantity, $category, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='product.php';</script>";
    } else {
        echo "<script>alert('Error updating product');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="edit_product.css"> <!-- External CSS -->
    
    <div class="sidebar">
        <div class="logo-container">
            <img src="logo.png" alt="Zoey Food Hub Logo" class="logo">
            <h2>ZOEY FOOD HUB</h2>
        </div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="product.php"><i class="fas fa-users"></i> Product</a></li>
            <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
            <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales Records</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
        </ul>
    </div>
</head>
<body>


<div class="main-content">
    <h2>Edit Product</h2>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= $product['Name']; ?>" required><br>

        <label>Description:</label>
        <textarea name="description" required><?= $product['Description']; ?></textarea><br>

        <label>Price (₱):</label>
        <input type="number" step="0.01" name="price" value="<?= $product['Price']; ?>" required><br>

        <label>Stock Quantity:</label>
        <input type="number" name="stock_quantity" value="<?= $product['Stock_Quantity']; ?>" required><br>

        <label>Category:</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat; ?>" <?= ($cat == $product['Category']) ? 'selected' : ''; ?>><?= $cat; ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit">Update Product</button>
    </form>
</div>
</body>
</html>

<?php $conn->close(); ?>
