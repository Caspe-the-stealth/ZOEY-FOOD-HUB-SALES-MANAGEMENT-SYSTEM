<?php
include 'db_connection.php';
include 'side_admin.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle ADD order logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {
    $order_date   = $_POST['order_date'];
    $order_status = $_POST['order_status']; // e.g., Pending, Completed, or Cancelled
    $customer_id  = $_POST['customer_id'];
    $product_id   = $_POST['product_id'];
    $price        = $_POST['price'];
    
    // Using uppercase column names for orders table: Customer_ID and Product_ID
    $stmt = $conn->prepare("INSERT INTO orders (Order_Date, Order_Status, Customer_ID, Product_ID, Total_Amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiid", $order_date, $order_status, $customer_id, $product_id, $price);
    $stmt->execute();
    $stmt->close();
    header("Location: orders.php");
    exit;
}

// Fetch customers for dropdown (assuming the customers table has customer_id, first_name, last_name)
$customerQuery = "SELECT * FROM customers";
$customerResult = $conn->query($customerQuery);

// Fetch products for dropdown (assuming the products table has Product_ID, Name, Price)
$productQuery = "SELECT * FROM products";
$productResult = $conn->query($productQuery);

// Fetch all orders by joining orders with customers and products
// Note: orders table columns are Customer_ID and Product_ID (uppercase) while customers use customer_id.
$query = "SELECT o.Order_ID, o.Order_Date, o.Order_Status, 
                 c.first_name, c.last_name, 
                 p.Name AS ProductName, o.Total_Amount 
          FROM orders o 
          INNER JOIN customers c ON o.Customer_ID = c.customer_id 
          INNER JOIN products p ON o.Product_ID = p.Product_ID 
          ORDER BY o.Order_Date DESC";
$result = $conn->query($query);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>


</head>
<body>
  
    </div>
    
    <div class="main-content">
        <!-- Left Side: Add Order Form -->
        <div class="form-container">
            <h2>Add Order</h2>
            <form action="orders.php" method="post">
                <label>Order Date:</label>
                <input type="date" name="order_date" required>
                
                <label>Order Status:</label>
                <select name="order_status" required>
                    <option value="">Select Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                
                <label>Select Customer:</label>
                <select name="customer_id" required>
                    <option value="">Select Customer</option>
                    <?php while ($cust = $customerResult->fetch_assoc()): ?>
                        <option value="<?= $cust['customer_id']; ?>">
                            <?= htmlspecialchars($cust['first_name'] . ' ' . $cust['last_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label>Select Product:</label>
                <select name="product_id" required>
                    <option value="">Select Product</option>
                    <?php while ($prod = $productResult->fetch_assoc()): ?>
                        <option value="<?= $prod['Product_ID']; ?>" data-price="<?= htmlspecialchars($prod['Price']); ?>">
                            <?= htmlspecialchars($prod['Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label>Total Amount:</label>
                <input type="number" name="price" step="0.01" placeholder="Price" required>
                <br><br>
                <button type="submit" name="add_order">Add Order</button>
            </form>
        </div>
        
        <!-- Right Side: Orders Table -->
        <div class="table-container">
            <h2>Orders List</h2>
            <table>
                <tr>
                    <th>Customer</th>
                    <th>Product Name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        <td><?= htmlspecialchars($row['ProductName']); ?></td>
                        <td>₱<?= number_format($row['Total_Amount'], 2); ?></td>
                        <td><?= htmlspecialchars($row['Order_Status']); ?></td>
                        <td>
                            <a href="edit_order.php?id=<?= $row['Order_ID']; ?>" class="edit-btn">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
    <script>
    document.querySelector('select[name="product_id"]').addEventListener('change', function(){
        var price = this.options[this.selectedIndex].getAttribute('data-price') || '';
        document.querySelector('input[name="price"]').value = price;
    });
    </script>
</body>
</html>