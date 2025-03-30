<?php
include 'db_connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure order id is provided
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}
$order_id = $_GET['id'];

// Fetch order details
$stmt = $conn->prepare("SELECT Order_Date, Order_Status, Customer_ID, Product_ID, Total_Amount FROM orders WHERE Order_ID = ?");
if (!$stmt) { die("Prepare failed: " . $conn->error); }  // <-- added error check
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($order_date, $order_status, $customer_id, $product_id, $total_amount);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: orders.php");
    exit;
}
$stmt->close();

// Fetch customers for dropdown
$customerResult = $conn->query("SELECT customer_id, first_name, last_name FROM customers");

// Fetch products for dropdown
$productResult = $conn->query("SELECT Product_ID, Name FROM products");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $new_order_date = $_POST['order_date'];
    $new_order_status = $_POST['order_status'];
    $new_customer_id = $_POST['customer_id'];
    $new_product_id = $_POST['product_id'];
    $new_total_amount = $_POST['total_amount'];
    
    $update_stmt = $conn->prepare("UPDATE orders SET Order_Date = ?, Order_Status = ?, Customer_ID = ?, Product_ID = ?, Total_Amount = ? WHERE Order_ID = ?");
    if (!$update_stmt) { die("Prepare failed: " . $conn->error); }  // <-- added error check
    $update_stmt->bind_param("ssiidi", $new_order_date, $new_order_status, $new_customer_id, $new_product_id, $new_total_amount, $order_id);
    $update_stmt->execute();
    $update_stmt->close();
    
    header("Location: orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="orders.css">
    <style>
        /* Minimal styling for the edit order form */
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #242424;
            border-radius: 8px;
            box-shadow: 2px 2px 15px rgba(255,255,255,0.1);
            color: #E0E0E0;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-container label {
            font-size: 16px;
        }
        .form-container input, .form-container select {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #2D2D2D;
            color: #E0E0E0;
        }
        .form-container button {
            background-color: #4DA8DA;
            border: none;
            padding: 12px;
            border-radius: 5px;
            color: #FFF;
            font-size: 16px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #77D7F9;
        }

        
    /* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #121212;
    color: #E0E0E0;
    margin: 0;
    padding: 0;
}

/* Sidebar */
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
    margin-bottom: 20px;
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
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="images (1).png" alt="Zoey Food Hub Logo" style="width: 100px; height: 100px; border-radius: 50%;">
            <h2>ZOEY FOOD HUB</h2>
        </div>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="customers.php">Customers</a></li>
            <li><a href="product.php">Product</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="sales.php">Sales Records</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </div>

<div class="form-container">
    <h2>Edit Order</h2>
    <form method="post" action="edit_order.php?id=<?= htmlspecialchars($order_id); ?>">
        <label>Order Date:</label>
        <input type="date" name="order_date" value="<?= htmlspecialchars($order_date); ?>" required>
        
        <label>Order Status:</label>
        <select name="order_status" required>
            <option value="">Select Status</option>
            <option value="Pending" <?= ($order_status === "Pending") ? 'selected' : ''; ?>>Pending</option>
            <option value="Completed" <?= ($order_status === "Completed") ? 'selected' : ''; ?>>Completed</option>
            <option value="Cancelled" <?= ($order_status === "Cancelled") ? 'selected' : ''; ?>>Cancelled</option>
        </select>
        
        <label>Select Customer:</label>
        <select name="customer_id" required>
            <option value="">Select Customer</option>
            <?php while ($cust = $customerResult->fetch_assoc()): ?>
                <option value="<?= $cust['customer_id']; ?>" <?= ($cust['customer_id'] == $customer_id) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($cust['first_name'] . ' ' . $cust['last_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <label>Select Product:</label>
        <select name="product_id" required>
            <option value="">Select Product</option>
            <?php while ($prod = $productResult->fetch_assoc()): ?>
                <option value="<?= $prod['Product_ID']; ?>" <?= ($prod['Product_ID'] == $product_id) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($prod['Name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <label>Total Amount:</label>
        <input type="number" name="total_amount" step="0.01" value="<?= htmlspecialchars($total_amount); ?>" required>
        
        <button type="submit" name="update_order">Update Order</button>
    </form>
</div>
</body>
</html>
