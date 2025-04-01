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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<style>
/* Customized styling for Sales Page */
.main-content {
    margin-left: 270px;
    padding: 30px;
    width: calc(100% - 270px);
    background: #181818;
    min-height: 100vh;
    color: #E0E0E0;
}

h2 {
    text-align: center;
    color: #F0F0F0;
    margin-bottom: 25px;
    font-size: 24px;
}

/* New sticky Sales Summary styling */
.sales-summary-wrapper {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #181818; /* Same as main background for consistency */
    padding-top: 10px;
    margin-bottom: 20px;
}

/* Adjusted Sales Summary Styling */
.summary {
    display: inline-block;
    background: #242424;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 2px 2px 15px rgba(255, 255, 255, 0.1);
}

.summary h3 {
    margin-top: 0;
    color: #FFFFFF;
}

.summary p {
    font-size: 16px;
    margin: 5px 0;
    color: #CCCCCC;
}

/* Notification Styling */
.notification {
    background: #2D2D2D;
    padding: 10px 20px;
    border-left: 3px solid #4DA8DA;
    border-radius: 4px;
    margin-bottom: 20px;
    color: #E0E0E0;
}

/* Table Styling Consistency */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: #222;
    margin-bottom: 20px;
    border-radius: 8px;
    overflow: hidden;
}

thead th {
    background-color: #444;
    color: #FFFFFF;
    padding: 12px;
    text-align: left;
    font-size: 16px;
}

tbody td {
    padding: 12px;
    border-bottom: 1px solid #333;
    color: #E0E0E0;
}

tbody tr:nth-child(even) {
    background-color: #2A2A2A;
}

tbody tr:hover {
    background-color: #3A3A3A;
}
  /* General Styles */
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #121212;
    color: #E0E0E0;
    margin: 0;
    padding: 0;
}
   /* Sidebar and general styling */
   body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #E0E0E0;
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
        .sidebar .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar .logo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: auto;
        }
        .sidebar h2 {
            font-size: 24px;
            color: #FFFFFF;
            margin: 0;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 14px 25px;
            color: #CCCCCC;
            text-decoration: none;
            font-size: 18px;
            transition: 0.3s ease-in-out;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #333333;
            color: #FFFFFF;
            font-weight: bold;
        }
        .sidebar ul li a i {
            margin-right: 15px;
        }

/* Main Content */
.main-content {
    margin-left: 270px;
    padding: 30px;
    width: calc(100% - 270px);
    background: #181818;
    min-height: 100vh;
}


</style>



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
   <h1>Sales Records</h1>
   <!-- Display notification if available -->
   <?php
   if (isset($_SESSION['success'])) {
       echo "<div class='notification'>" . htmlspecialchars($_SESSION['success']) . "</div>";
       unset($_SESSION['success']);
   }
   ?>
   <!-- Place Sales Summary immediately above the table -->
   <div class="sales-summary-wrapper">
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
   </div>
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