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

// Query only completed orders (sales records) joined with products to get product name
$order_query  = "SELECT o.Order_ID, p.Name AS product_name, o.Total_Amount, o.Order_Date, o.Order_Status 
                  FROM orders o 
                  JOIN products p ON o.Product_ID = p.Product_ID 
                  WHERE o.Order_Status = 'Completed'";
$order_result = mysqli_query($conn, $order_query);

// NEW: Query for Total Sales
$total_sales_query = "SELECT SUM(Total_Amount) AS total_sales FROM orders WHERE Order_Status = 'Completed'";
$total_sales_result = mysqli_query($conn, $total_sales_query);
$total_sales_row = mysqli_fetch_assoc($total_sales_result);
$total_sales = $total_sales_row['total_sales'];

// NEW: Query for Best Sale (order with highest Total_Amount)
$best_sale_query = "SELECT o.Order_ID, p.Name AS product_name, o.Total_Amount, o.Order_Date 
                    FROM orders o 
                    JOIN products p ON o.Product_ID = p.Product_ID 
                    WHERE o.Order_Status = 'Completed'
                    ORDER BY o.Total_Amount DESC LIMIT 1";
$best_sale_result = mysqli_query($conn, $best_sale_query);
$best_sale = mysqli_fetch_assoc($best_sale_result);
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
            width: 250px;
            background: #1E1E1E;
            height: 100vh;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 3px 0 20px rgba(0, 0, 0, 0.4);
        }
        .sidebar .logo {
             width: 150px;
             height: 150px;
             border-radius: 50%;
             object-fit: cover;
             display: block;
             margin: auto;
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
        .summary {
            max-width: 800px;
            margin: 20px auto;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 4px;
            text-align: center;
            color: #333;
        }
        /* Rounded Logo */
.sidebar .logo {
  width: 150px;
  height: 150px;
  border-radius: 50%; /* Makes the image fully rounded */
  object-fit: cover;  /* Ensures image fills and maintains aspect ratio */
  display: block;
  margin: auto;
}
.logo {
    width: 150px; /* Adjust size as needed */
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
    margin: auto;
}
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
    <img src="images (1).png" alt="Zoey Food Hub Logo" style="width:250px; height:100px; border-radius:80%; object-fit:cover; display:block; margin:auto;">

        <h2>ZOEY FOOD HUB</h2>
    </div>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a href="product.php">Product</a></li>
        <li><a href="orders.php">Orders</a></li>
        <li><a href="sales.php">Sales Records</a></li>
        <li><a href="login.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
   <h2>Sales Records</h2>
   <!-- NEW: Sales Summary Headers -->
   <div class="summary">
       <h3>Sales Summary</h3>
       <p><strong>Total Sales:</strong> ₱<?php echo number_format($total_sales, 2); ?></p>
       <?php if ($best_sale): ?>
         <p>
             <strong>Best Sale:</strong> Order <?php echo htmlspecialchars($best_sale['Order_ID']); ?> - 
             <?php echo htmlspecialchars($best_sale['product_name']); ?> (₱<?php echo number_format($best_sale['Total_Amount'], 2); ?> on <?php echo htmlspecialchars($best_sale['Order_Date']); ?>)
         </p>
       <?php endif; ?>
   </div>
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
            <th>Order Date</th>
            <th>Total Amount (₱)</th>
            <th>Status</th>
        </tr>
        <?php while ($order = mysqli_fetch_assoc($order_result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['Order_ID']); ?></td>
            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
            <td><?php echo htmlspecialchars($order['Order_Date']); ?></td>
            <td>₱<?php echo number_format($order['Total_Amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($order['Order_Status']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
<?php
$conn->close();
?>