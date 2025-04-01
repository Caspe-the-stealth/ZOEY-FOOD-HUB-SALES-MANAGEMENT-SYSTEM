<?php
ob_start(); // Start output buffering to avoid "headers already sent" error
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
    <style>
        /* Hide all scrollbars */
        body {
            overflow: hidden; // hide vertical & horizontal scroll
            margin: 0;
            padding: 0;
        }
        /* Main content adjusted */
        .main-content {
            height: 100px; /* reduced overall height */
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            align-items: flex-start;
            gap: 20px;
            overflow: hidden;
        }
        .main-content {
    margin-left: 270px;
    padding: 0px;
    width: calc(100% - 270px);
    background: #181818;
    min-height: 99vh;
}
        /* Reduce width for balanced layout */
        .form-container,
        .table-container {
            width: 50%; /* reduced width for both sections */
            background: #2C2C2C; // eye-catching background
            padding: 14px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.5);
            overflow: hidden;
        }
        /* Revised styling for the Add Order form */
        .form-container {
            background: #242424; // consistent admin theme color
            padding: 11px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            transition: transform 0.3s;
        }
        .form-container:hover {
            transform: scale(1.02);         // subtle hover scale effect
        }
        .form-container h2 {
            color: #F0F0F0;
            font-size: 20px;
            margin-bottom: 10px;
            text-align: center;
        }
        .form-container form {
            width: 100%;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 0 auto;
        }
        .form-container form button {
            width: 100%;
            padding: 10px; /* button clearly visible */
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .form-container form button:hover {
            background-color: #45a049;
        }
        
        /* Adjust form elements for improved consistency and appearance */
        .form-container form label {
            color: #F0F0F0;
            font-size: 14px;
            font-weight: bold;
        }
        .form-container form input,
        .form-container form select {
            width: 100%;
            padding: 10px;
            background: #181818;
            color: #E0E0E0;
            border: 1px solid #444;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 1px;
            height: 100;
        }
        .form-container form button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1px;
        }
        .form-container form button:hover {
            background-color: #45a049;
        }

        /* Adjustments for the Orders List table */
        .table-container {
            background: #242424; // matching admin card
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container th,
        .table-container td {
            padding: 12px;
            border-bottom: 1px solid #444;
            text-align: left;
            color: #F0F0F0;
        }
        .table-container th {
            background: #1E1E1E;
            font-size: 16px;
        }
        .table-container tr:hover {
            background: #333;
        }
    </style>
</head>
<body>
<div class="sidebar">
<div class="logo-container">
<img src="images (1).png" alt="Zoey Food Hub Logo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin: auto;">
<br>  
<h2>ZOEY FOOD HUB</h2>
        </div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="product.php"><i class="fas fa-users"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
            <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
            <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
        </ul>
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
                <button type="submit" name="add_order">Add Order</button>
                <br>
                <br>
                <br>
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
<?php
ob_end_flush();
?>
</body>
</html>