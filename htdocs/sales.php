<?php
session_start();
include 'db_connection.php'; // Ensure this file connects to your database

// Handle product addition to cart (if coming from, for example, a product list)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id    = $_POST['product_id'];
    $product_name  = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    
    // Initialize cart if not set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add product to cart (increment quantity if exists)
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name'     => $product_name,
            'price'    => $product_price,
            'quantity' => 1
        ];
    }
    
    // Redirect to sales.php so the new order will appear after being processed in cart.php
    header('Location: sales.php');
    exit();
}

// Query all orders (sales records)
// You can adjust the query as needed. Here we assume the orders table contains
// columns: order_id, product_name, quantity, price, order_date, total_payment, status
$order_query  = "SELECT * FROM orders";
$order_result = mysqli_query($conn, $order_query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Records</title>
    <link rel="stylesheet" href="sales.css">
    <style>
        /* Simple styling - adjust as needed */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #333;
            float: left;
            color: #fff;
            padding: 20px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar li {
            margin-bottom: 15px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        .notification {
            max-width: 800px;
            margin: 20px auto;
            padding: 12px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            text-align: center;
        }
        form.status-form select {
            padding: 4px;
            font-size: 14px;
        }
        form.status-form button {
            padding: 4px 8px;
            font-size: 14px;
            margin-left: 8px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="logo.png" alt="Zoey Food Hub Logo" class="logo" style="width:80%;">
        <h2>ZOEY FOOD HUB</h2>
    </div>
    <ul>
        <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="product.php"><i class="fas fa-box"></i> Product</a></li>
        <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
        <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
   <h2>Sales Records</h2>
   <!-- Display notification if available -->
   <?php
   if (isset($_SESSION['success'])) {
       echo "<div class='notification'>" . htmlspecialchars($_SESSION['success']) . "</div>";
       unset($_SESSION['success']);
   }
   ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price (₱)</th>
            <th>Order Date</th>
            <th>Total Payment (₱)</th>
            <th>Status</th>
        </tr>
        <?php
        while ($order = mysqli_fetch_assoc($order_result)) {
        ?>
        <tr>
            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
            <td><?php echo htmlspecialchars($order['quantity']); ?></td>
            <td><?php echo htmlspecialchars($order['price']); ?></td>
            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
            <td><?php echo htmlspecialchars($order['total_payment']); ?></td>
            <td>
                <form action="update_status.php" method="POST" class="status-form">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <select name="status">
                        <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Complete" <?php if ($order['status'] == 'Complete') echo 'selected'; ?>>Complete</option>
                        <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php } // end while ?>
    </table>
</div>

</body>
</html>
<?php
$conn->close();
?>