<?php
include 'db_connection.php'; // Include database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize and validate input data
    $name = trim($_POST['name']) ?? '';
    $description = trim($_POST['description']) ?? '';
    $price = $_POST['price'] ?? 0;
    $stock_quantity = $_POST['stock_quantity'] ?? 0;
    $category = trim($_POST['category']) ?? '';
    
    if (empty($name) || empty($description) || empty($category) || $price <= 0 || $stock_quantity < 0) {
        echo 'Invalid input. Please check your data and try again.';
        exit;
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO products (Name, Description, Price, Stock_Quantity, Category) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssdss", $name, $description, $price, $stock_quantity, $category);
        
        if ($stmt->execute()) {
            echo 'Product added successfully!';
        } else {
            echo 'Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Database error: Unable to prepare statement.';
    }
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
